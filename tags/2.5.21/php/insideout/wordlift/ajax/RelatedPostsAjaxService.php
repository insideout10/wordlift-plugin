<?php
/**
 * User: David Riccitelli
 * Date: 20/09/12 17:38
 */

class WordLift_RelatedPostsAjaxService {

    /** @var WordLift_EntityService $entityService */
    public $entityService;

    public function get( $postID ) {
        return $this->entityService->findRelated( $postID );
    }

}

?>