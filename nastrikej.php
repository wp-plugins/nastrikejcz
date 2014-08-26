<?php

/*
  Plugin Name: Nastříkej
  Plugin URI: http://www.nastrikej.cz
  Description: Rozpis hasičských soutěží/výsledků z celé ČR
  Version: 0.2
  Author: vEnCa-X
  Author URI: http://www.venca-x.cz
  License: MIT
 */

class Nastrikej extends WP_Widget
{

    protected $url = "http://www.nastrikej.cz/json/";

    public function __construct()
    {
        parent::__construct( false, $name = __( 'Nastříkej' ) );
    }

    public function form( $instance )
    {
        $instance = wp_parse_args(
                (array) $instance
        );

        if ( !empty( $instance['title'] ) )
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __( 'Soutěže', 'wptuts-countdowner-locale' );
        }
        
        if ( !empty( $instance['seasonId'] ) )
        {
            $seasonId = $instance['seasonId'];
        }
        else
        {
            $seasonId = "";
        }
        
        if ( !empty( $instance['teamsId'] ) )
        {
            $teamsId = $instance['teamsId'];
        }
        else
        {
            $teamsId = array();
        }        

        // Display the admin form
        include( plugin_dir_path( __FILE__ ) . '/views/admin.php' );
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

        $title = apply_filters( 'wptuts_countdowner_title', $instance['title'] );
        $seasonId = apply_filters( 'season_id', $instance['seasonId'] );
                
        $url = $this->url . "competition-in-season/" . $seasonId;
        if( is_array( $instance['teamsId'] ) )
        {
            $url.= "?team=" . implode( "x", $instance['teamsId'] );
        }

        //echo "".$url."";
                
        $json = file_get_contents( $url );
        $data = json_decode( $json, TRUE );
        
        $table = "<table id='nastrikej'>
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
                                    //,{$result["position"]}.místo";
                                $table.="</tr>";                            
                            }
                        }
                        
                    }                
        $table.= "</table>";
                
        include( plugin_dir_path( __FILE__ ) . '/views/widget.php' );

        echo $after_widget;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("nastrikej");'));

?>