<?php

class WordLift_OptionsPage
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
        . "/html/options.html";
    }
}

?>