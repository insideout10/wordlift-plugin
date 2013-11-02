WordLift Plug-in for WordPress
==============================

## Overview

The official WordLift Web Site: [wordlift.it](http://wordlift.it)

## Installation

## Configuration

## Usage

### AJAX APIs

#### Action *wordlift.job*

```
POST http://.../wp-admin/admin-ajax.php?action=wordlift.job&postID={postID}

GET http://.../wp-admin/admin-ajax.php?action=wordlift.job&jobID={jobID}

GET http://.../wp-admin/admin-ajax.php?action=wordlift.job&postID={postID}

PUT http://.../wp-admin/admin-ajax.php?action=wordlift.job&jobID={jobID}&jobState={jobState}
```

#### Action *wordlift.post/entites*

```
POST http://.../wp-admin/admin-ajax.php?action=wordlift.post/entities&postID={postID}&entity={entity}&textAnnotation={textAnnotation}
```

Parameters:

* **postID**, a numeric postID
* **entity**, e.g. http://dbpedia.org/resource/Guatemala_City
* **textAnnotation**, e.g. urn:enhancement-c010b535-3933-b0f4-752b-9e909b65aa37

## License

TBD