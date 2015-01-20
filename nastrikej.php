<?php

/*
  Plugin Name: Nastříkej
  Plugin URI: http://www.nastrikej.cz
  Description: Rozpis hasičských soutěží/výsledků z celé ČR
  Version: 0.3
  Author: vEnCa-X
  Author URI: http://www.venca-x.cz
  License: MIT
 */

define("URL", "http://www.nastrikej.cz/json/");
    
class NastrikejCompetitions extends WP_Widget
{

    public function __construct()
    {
        parent::__construct( false, $name = __( 'Nastříkej - soutěže' ) );
    }

    public function form( $instance )
    {
        $instance = wp_parse_args( (array) $instance );

        if ( isset( $instance['title'] ) )
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __( 'Soutěže', 'wptuts-countdowner-locale' );
        }

        if ( isset( $instance['seasonId'] ) )
        {
            $seasonId = $instance['seasonId'];
        }
        else
        {
            $seasonId = "";
        }
        
        if ( isset( $instance['teamsId'] ) )
        {
            $teamsId = $instance['teamsId'];
        }
        else
        {
            $teamsId = array();
        }        

        // Display the admin form
        include( plugin_dir_path( __FILE__ ) . '/views/admin-competitions.php' );
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['seasonId'] = strip_tags( $new_instance['seasonId'] );
        $instance['teamsId'] = esc_sql( $new_instance['teamsId'] );        
        return $instance;
    }

    public function widget( $args, $instance )
    {
        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $seasonId = $instance['seasonId'];

        $url = URL . "competition-in-season/" . $seasonId;
        if( is_array( $instance['teamsId'] ) )
        {
            $url.= "?team=" . implode( "x", $instance['teamsId'] );
        }

        //echo "".$url."";

        $json = file_get_contents( $url );
        $data = json_decode( $json, TRUE );

        $table = "<table class='nastrikej'>
                    <tr>
                        <th>Datum</th>
                        <th>Místo</th>
                    </tr>";
        foreach ( $data["competitions"] as $competition )
        {
            $table.="<tr>";
            $table.="<td class='competition'>" . $competition["date"] . "&nbsp;</td><td class='competition-village'><a href='" . $competition["href"] . "'>" . $competition["village"] . "</a></td>";
            $table.="</tr>";

            if( count( $competition["results"] ) > 0 )
            {
                foreach( $competition["results"] as $result)
                {
                    $video = "";
                    foreach( $result["youtubes"] as $youtube )
                    {
                        $video.= " <a href='" . $youtube . "' target='_blank' class='competition-video'><img src='http://www.nastrikej.cz/pictures/video-icon.png' alt='video'></a> ";
                    }

                    $table.="<tr>";
                    $table.="<td colspan=\"2\" class='competition-result'><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$result["position"]}. {$result["team"]}, čas: {$result["time"]}</small>".$video."</td>";
                    $table.="</tr>";
                }
            }
        }

        $table.= "</table>";

        include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

        echo $after_widget;
    }

}

class NastrikejPositions extends WP_Widget
{

    public function __construct()
    {
        parent::__construct( false, $name = __( 'Nastříkej - pořadí' ) );
    }

    public function form( $instance )
    {
        $instance = wp_parse_args(
            (array) $instance
        );

        if ( isset( $instance['title'] ) )
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __( 'Pořadí', 'wptuts-countdowner-locale' );
        }

        if ( isset( $instance['seasonId'] ) )
        {
            $seasonId = $instance['seasonId'];
        }
        else
        {
            $seasonId = "";
        }

        if ( isset( $instance['teamsId'] ) )
        {
            $teamsId = $instance['teamsId'];
        }
        else
        {
            $teamsId = array();
        }

        if ( isset( $instance['countTeamsAround'] ) )
        {
            $countTeamsAround = $instance['countTeamsAround'];
        }
        else
        {
            $countTeamsAround = -1;
        }

        // Display the admin form
        include( plugin_dir_path( __FILE__ ) . '/views/admin-positions.php' );
    }

    public function update( $new_instance, $old_instance )
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['seasonId'] = strip_tags( $new_instance['seasonId'] );
        $instance['teamsId'] = esc_sql( $new_instance['teamsId'] );
        $instance['countTeamsAround'] = strip_tags( $new_instance['countTeamsAround'] );
        return $instance;
    }

    public function widget( $args, $instance )
    {
        extract( $args, EXTR_SKIP );

        echo $before_widget;

        $title = apply_filters('widget_title', $instance['title']);
        $seasonId = $instance['seasonId'];
        if(isset($instance['countTeamsAround']))
        {
            $countTeamsAround = $instance['countTeamsAround'];
        }

        //positions
        $url = URL . "positions-in-season/" . $seasonId;
        if( is_array( $instance['teamsId'] ) )
        {
            $url.= "?team=" . implode( "x", $instance['teamsId'] );
        }

        if(isset($countTeamsAround))
        {
            $url .= "&countTeamsAround=" . $countTeamsAround;
        }

        $json = file_get_contents( $url );
        $data = json_decode( $json, TRUE );

        //positions
        $table = "<table class='nastrikej'>
                    <tr>
                        <th>Pořadí</th>
                        <th>Družstvo</th>
                        <th>Body</th>
                    </tr>";

        foreach( $data["positions"] as $aRowPosition )
        {
            $strongStartTag = "";
            $strongEndTag = "";
            if($aRowPosition['strong'] === TRUE)
            {
                $strongStartTag = "<strong>";
                $strongEndTag = "</strong>";
            }
            $table.= "<tr>
                            <td>{$strongStartTag}{$aRowPosition['position']}{$strongEndTag}.</td>
                            <td><a href='{$aRowPosition['team_href']}' target='_blank'>{$strongStartTag}{$aRowPosition['team']}{$strongEndTag}</a></td>
                            <td>{$strongStartTag}{$aRowPosition['points']}{$strongEndTag}</td>
                        </tr>";
        }

        $table.= "</table>";

        include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

        echo $after_widget;
    }

}


add_action('widgets_init', create_function('', 'return register_widget("nastrikejCompetitions");'));
add_action('widgets_init', create_function('', 'return register_widget("nastrikejPositions");'));

?>