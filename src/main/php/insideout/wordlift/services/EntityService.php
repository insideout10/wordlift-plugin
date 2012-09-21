<?php

interface WordLift_EntityService {

    public function findAll();

    public function findRelated( $postID );

    public function getByPostID( $postID );

    public function getBySubject( $subject );

    public function create( $subject );

    public function bindPostToSubjects( $postID, $subject );

}

?>