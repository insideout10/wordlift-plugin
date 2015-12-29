<?php

class WordLift_JobsPage
{
    public function get() {
        include dirname(
            dirname(
                dirname(
                    dirname(
                        dirname( __FILE__ )
                    )
                )
            )
        )
        . "/html/wordlift-settings.html";
    }
}

?>