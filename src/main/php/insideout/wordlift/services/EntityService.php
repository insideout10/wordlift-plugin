<?php

interface WordLift_EntityService {

    public function getBySubject( $subject );

    public function create( $subject );

    public function bindPostToSubjects( $postID, $subject );

}

?>