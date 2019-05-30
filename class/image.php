<?php

/*
Sincere apologies for this stupid list of functions that look like a class.

Please re-write this properly when you have a little time. JLG
*/
include_once 'config.php';

class Image {

	private $conn = null;
	private $caption = null;
	private $path = null;
	private $basename = null;
	private $refid = null;
	private $created = null;

	public function __construct( $data = array() ) {
		$this->conn = new PDO( DB_DSN, DB_USERNAME, DB_PASSWORD, array(
    		                       PDO::ATTR_PERSISTENT => true
		));
		$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function __destruct() {
	}

	public function getListingImageSrc($imageid, $width=100, $imgidx=0) {
		//$imageid = $this->getImage($userid, 'profile', $imgidx);
		$image = $this->getImagePath($imageid);
		$src = '/image.php?id='.$imageid.'&amp;width='.$width;
		return $src;
	}

	public function getProfileImageSrc($userid, $width=100, $imgidx=0) {
		$imageid = $this->getImage($userid, 'profile', $imgidx);
		$image = $this->getImagePath($imageid);
		$src = '/image.php?id='.$imageid.'&amp;width='.$width;
		return $src;
	}

	public function getImage($refid, $imagetype, $imgidx=0, $recurse=true) {
		$img = $this->getAllDBImages($refid, $imagetype, $recurse);
		$imageid = isset($img[$imgidx]) ? $img[$imgidx] : false;
		return($imageid);
	}

    public function getImageByPos($refid, $imagetype, $position) {
		try {

			$images = array();
			$sql = "SELECT imageid FROM images WHERE refid = :refid and imagetype = :imagetype and priority = :priority";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);
			$stmt->bindvalue(":priority", $position, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
				if ($returndefault == false) {
					return false;
				} else  {
				    $images = $stmt->fetch(PDO::FETCH_COLUMN);
				}
			}

			return($images);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return array();
	}

    public function getImageByPosByUserID($userid, $refid, $imagetype, $position) {
		try {

			$images = array();
			$sql = "SELECT imageid FROM images WHERE refid = :refid and userid = :userid and imagetype = :imagetype and priority = :priority";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);
			$stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);
			$stmt->bindvalue(":priority", $position, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
				if ($returndefault == false) {
					return false;
				} else  {
				    $images = $stmt->fetch(PDO::FETCH_COLUMN);
				}
			}

			return($images);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return array();
	}



	public function getMainImage($refid, $imagetype, $returndefault=true) {
		$images = $this->getAllDBImages($refid, $imagetype, $returndefault);
        if(isset($images[0]))
		    return $images[0];
        else
            return null;
	}

    # I need the userid so people can't screw with other people's photos
	public function makePrimary($imageid, $imagetype, $userid) {
		try {

            if($userid != $this->getOwner($imageid)) {
                error_log("ABUSE: Attempt to rearrange another person's image by $userid");
                throw new Exception("You cannot rearrange other people's images.");
            }

			$sql = "SELECT imageid,refid,imagetype,caption,path,basename,private,created,priority FROM images WHERE imageid = :imageid and imagetype = :imagetype and userid = :userid";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":imageid", $imageid, PDO::PARAM_INT);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);
			$stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
					$images = $this->getAllDBImages(1, $imagetype, false);
			}

			$returned = $stmt->fetch(PDO::FETCH_NUM);
            //error_log("SELECT imageid,refid,imagetype,caption,path,basename,private,created,priority FROM images WHERE imageid = $imageid and imagetype = $imagetype and userid = $userid");
            //error_log(print_r($returned,true));
			list($dbimageid,$dbrefid,$dbimagetype,$dbcaption,$dbpath,$dbbasename,$dbprivate,$dbcreated,$dbpriority) = $returned;

            $allImages = $this->getAllDbImages($dbrefid, $imagetype);
            $count = 1;
			$sql = "UPDATE images set priority = :priority where imageid = :imageid";
            $updatestmt = $this->conn->prepare( $sql );
            //error_log($sql);
            $_SESSION['tmpimageid'] = array();
            foreach($allImages as $loopimageid) {
                if($loopimageid == $imageid) {
                    $_SESSION['tmpimageid'][0] = $loopimageid;
                    $updatestmt->bindvalue(":priority", 0, PDO::PARAM_INT);
                    $updatestmt->bindvalue(":imageid", $loopimageid, PDO::PARAM_INT);
                } else {
                    $_SESSION['tmpimageid'][$count] = $loopimageid;
                    $updatestmt->bindvalue(":priority", $count++, PDO::PARAM_INT);
                    $updatestmt->bindvalue(":imageid", $loopimageid, PDO::PARAM_INT);
                }
                $updatestmt->execute();
            }

            return true;

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
        return false;
	
    }

    /* Only use this for images viewed by non-owner */
	public function getAllDBImages($refid, $imagetype, $returndefault=false) {

		try {

			$images = array();
			$sql = "SELECT imageid FROM images WHERE refid = :refid and imagetype = :imagetype order by priority";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
				if ($returndefault == false) {
					return array();
				} else  {
					# Get the default image if it's available (luv recursion)
                    //error_log(print_r("GETTING DEFAULT IMAGE for refid=$refid, $imagetype",true));
					$images = $this->getAllDBImages(0, $imagetype, false);
				}
			} else {
				$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
			}

			return($images);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return array();
	}

	public function getAllDBImagesByUserID($userid, $refid, $imagetype) {

		try {

			$images = array();
			$sql = "SELECT imageid FROM images WHERE refid = :refid and userid = :userid and imagetype = :imagetype order by priority";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);
			$stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
					return array();
			} else {
				$images = $stmt->fetchAll(PDO::FETCH_COLUMN);
			}

			return($images);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return array();
	}


	public function getThumbPath($imageid) {

		try {

			$sql = "SELECT path, basename FROM images WHERE imageid = :imageid";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue("imageid", $imageid, PDO::PARAM_INT);

			$stmt->execute();


			list ($path, $basename) = $stmt->fetch();

			$path = rtrim($path, '/');
			if ($path == '')
				$filename = $basename;
			else
				$filename = $path.'/thumb_'.$basename;
				
			if (file_exists($filename)) 
				return ($filename);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return false;
	}



	public function getImagePath($imageid) {

		try {

			$sql = "SELECT path, basename FROM images WHERE imageid = :imageid";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue("imageid", $imageid, PDO::PARAM_INT);

			$stmt->execute();


			list ($path, $basename) = $stmt->fetch();

			$path = rtrim($path, '/');
			if ($path == '')
				$filename = $basename;
			else
				$filename = $path.'/'.$basename;
				
			if (file_exists($filename)) 
				return ($filename);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return false;
	}

	public function getRandomFilename($path, $ext, $depth=3) {
		$characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		$path = rtrim($path, '/');
 		for ($i = 0; $i < 20; $i++) {
			$rchar =  $characters[rand(0, strlen($characters) - 1)];
			if($i < $depth) {
				$path = $path.'/'.$rchar;
				if(!is_dir($path) )
					mkdir($path);
			}

			$string .= $rchar;
 		}
		$filename = $string.'.'.$ext;
		if(file_exists($filename))
			$filename = $this->getRandomFilename($path, $ext);

		return array($filename, $path.'/');
	}

	// Need to delete all the old images even if there is more than one.
	public function deleteOldProfileImages($userid, $skipid) {
			// Check to see if there is already an image
			$aImages = $this->getAllDBImagesByUserID($userid, $userid, 'profile');
            //error_log(print_r($aImages,true));
			foreach ($aImages as $oldimage) {
                if($skipid != $oldimage) {
				    error_log("DELETE OLD PROFILE IMG: $oldimage, $userid, $priority");
				    $this->deleteImage($oldimage, $userid);
                }
			}
	}


	// Need to delete all the old images even if there are more than one.
	public function deleteAllOldImages($userid, $imagetype) {
			// Check to see if there is already an image
            error_log("Deleting ALL OLD Images");
			$aImages = $this->getAllDBImagesByUserID($userid, 0, $imagetype);
			foreach ($aImages as $oldimage) {
				error_log("DELETE $oldimage, $userid, $imagetype, $priority");
				$this->deleteImage($oldimage, $userid);
			}
	}


	// Need to delete all the old images even if there are more than one.
	public function deleteOldImages($userid, $imagetype, $priority) {
			// Check to see if there is already an image
            //error_log("Deleting OLD Images");
			$aImages = $this->getAllDBImagesByUserID($userid, 0, $imagetype);
			foreach ($aImages as $oldimage) {
				$oldimageid = $this->getImageByPosByUserID($userid, 0, $imagetype, $priority);
				if($oldimageid) {
					//error_log("DELETE $oldimageid, $userid, $imagetype, $priority");
					$this->deleteImage($oldimageid, $userid);
				}
			}
	}

	public function deleteImage($imageid, $userid) {

		try {

            if($userid != $this->getOwner($imageid)) {
                error_log("ABUSE: Attempt to delete another person's image by $userid");
                throw new Exception("You cannot delete other people's images.");
            }

			$oldimage = $this->getImagePath($imageid);
			if($oldimage) {
				//error_log('UNLINK: '.$oldimage);
				if(unlink($oldimage) == false) {
					throw new Exception("ERROR: Cannot delete $oldimage");
				}
			}

			// Hitting it with a hammer - sorry
			$thumbnail = $this->getThumbPath($imageid);
			if($thumbnail) {
				if(unlink($thumbnail) == false) {
					throw new Exception("ERROR: Cannot delete $thumbnail");
				}
			}

			$sql = "DELETE FROM images where imageid = :imageid and userid = :userid";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":imageid", $imageid, PDO::PARAM_INT);
			$stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);

			//error_log("DELETE FROM images where imageid = $imageid and userid = $userid");
			$stmt->execute();

			$betterbefalse = $this->getImagePath($imageid);
			if($betterbefalse !== false)
				throw new Exception("ERROR: Deleted file $oldimage but not the DB entry for $imageid");

			return ($this->path.$this->basename);
		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return false;
	}


    public function getOwner($imageid) {
		try {

			$images = array();
			$sql = "SELECT userid FROM images WHERE imageid = :imageid";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":imageid", $imageid, PDO::PARAM_INT);

			$stmt->execute();

			$numRows = $stmt->rowCount();
			if( $numRows == 0 ) {
                return false;
			}

			$userid = $stmt->fetch(PDO::FETCH_COLUMN);
            //error_log("$userid UID");
			return($userid);

		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return false;
	}


	public function putDBImage($basename, $path, $refid, $userid, $priority, $imagetype, $caption = '') {

		try {
			// Check to see if there is already an image
			$this->deleteOldImages($userid, $imagetype, $priority, false);

			$sql = "INSERT INTO images (caption, path, imagetype, basename, refid, userid, priority) VALUES (:caption, :path, :imagetype, :basename, :refid, :userid, :priority)";

			$stmt = $this->conn->prepare( $sql );
			$stmt->bindvalue(":caption", $caption, PDO::PARAM_STR);
			$stmt->bindvalue(":path", $path, PDO::PARAM_STR);
			$stmt->bindvalue(":imagetype", $imagetype, PDO::PARAM_STR);
			$stmt->bindvalue(":basename", $basename, PDO::PARAM_STR);
			$stmt->bindvalue(":priority", $priority, PDO::PARAM_INT);
			$stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
			$stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);
            error_log("$sql : $caption $path $imagetype $basename $priority $refid $userid");

			$stmt->execute();

            $id = $this->conn->lastInsertID();
			return $id;
		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
		return false;
	}

	public function makeImagePermanent($imageid, $userid, $refid, $priority) {

		try {
           $oldpath = $this->getImagePath($imageid);

           # Sorry. This should occur in the function. Too tired.
           $ImageExt = substr($oldpath, strrpos($oldpath, '.'));
           $ImageExt = str_replace('.','',$ImageExt);
           list($filename, $newpath) = $this->getRandomFilename(IMGDESTDIR,$ImageExt);

		   $sql = "UPDATE images SET path=:path, basename=:basename, priority=:priority, refid=:refid where imageid=:imageid and userid=:userid";

		   $stmt = $this->conn->prepare( $sql );
		   $stmt->bindvalue(":path", $newpath, PDO::PARAM_STR);
		   $stmt->bindvalue(":imageid", $imageid, PDO::PARAM_INT);
		   $stmt->bindvalue(":basename", $filename, PDO::PARAM_STR);
		   $stmt->bindvalue(":priority", $priority, PDO::PARAM_INT);
		   $stmt->bindvalue(":refid", $refid, PDO::PARAM_INT);
		   $stmt->bindvalue(":userid", $userid, PDO::PARAM_INT);
           rename($oldpath, $newpath.$filename);

		   $stmt->execute();
		} catch ( PDOException $e) {
			error_log($e->getMessage());
		} catch ( Exception $e ) {
			error_log($e->getMessage());
		}
	}



	public function getImageFromFile($imagename, $width=0, $height=0) {

	}

	// Images available to everybody
	public function getSiteImage($imagename, $width, $height) {

	}


}

?>
