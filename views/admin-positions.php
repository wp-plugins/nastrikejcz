<?php
$hrefSeasons = "http://nastrikej.cz/json/wp"; //seznam roku a prislusnych lig   
?>


<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Titulek:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'seasonId' ); ?>"><?php _e( 'Sezóna:' ); ?></label>
    <?php
    $json = file_get_contents( $hrefSeasons );
    $jsonData = json_decode( $json, TRUE );

    $seasons = $jsonData["seasons"];
    ?>    
    <select id="<?php echo $this->get_field_id( 'seasonId' ); ?>" name="<?php echo $this->get_field_name( 'seasonId' ); ?>">
        <?php
        foreach ( $seasons as $year => $competitions )
        {
            echo "<optgroup label=\"{$year}\">";
            foreach ( $competitions as $competition )
            {
                echo "<option value=\"{$competition["season_id"]}\"";

                if ( $seasonId == $competition["season_id"] )
                {
                    echo " selected=\"selected\"";
                }
                echo ">{$competition["name"]} - {$year}</option>";
            }
            echo "</optgroup>";
        }
        ?>

    </select>
    <?php //echo "*".esc_attr( $seasonId )."*"; ?>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'teamsId' ); ?>"><?php _e( 'Zvýrazni výsledky týmů:' ); ?></label>
    <?php
    $teams = $jsonData["teams"];
    ?>    
    <select id="<?php echo $this->get_field_id( 'teamsId' ); ?>" name="<?php echo $this->get_field_name( 'teamsId' ); ?>[]" multiple style="height: 400px;">
        <?php
        foreach ( $teams as $name => $teamsCategory )
        {
            echo "<optgroup label=\"{$name}\">";
            foreach ( $teamsCategory as $team )
            {
                echo "<option value=\"{$team["team_id"]}\"";

                if ( in_array( $team["team_id"], $teamsId ) )
                {
                    echo " selected=\"selected\"";
                }
                echo ">{$team["name"]} - {$name}</option>";
            }
            echo "</optgroup>";
        }
        ?>

    </select>
</p>
<p>
    <label for="<?php echo $this->get_field_id( 'countTeamsAround' ); ?>"><?php _e( 'Počet týmů okolo:' ); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'countTeamsAround' ); ?>" name="<?php echo $this->get_field_name( 'countTeamsAround' ); ?>" type="text" value="<?php echo esc_attr( $countTeamsAround ); ?>" />
</p>