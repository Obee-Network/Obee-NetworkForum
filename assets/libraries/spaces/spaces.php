<?php

class SpacesConnect {
  /*
  An API wrapper for AWS, makes working with DigitalOcean's Spaces super easy.
  Written by Devang Srivastava for Dev Uncoded.
  Available under MIT License ( https://opensource.org/licenses/MIT )
  */

    function __construct($access_key, $secret_key, $spaceName = "", $region = "nyc3", $host = "digitaloceanspaces.com") {

        //Only pulled if an AWS class doesn't already exist.
        $non_composer_aws_lib = dirname(__FILE__)."/../s3/aws-autoloader.php";

        if(!empty($spaceName)) {
          $endpoint = "https://".$spaceName.".".$region.".".$host;
        }
        else {
          $endpoint = "https://".$region.".".$host;
        }
        if(!class_exists('Aws\S3\S3Client')) {
           if(file_exists($non_composer_aws_lib)) {
             require_once($non_composer_aws_lib);
           }
           else {
             throw new SpacesAPIException(@json_encode(["error" => ["message" => "No AWS class loaded.", "code" => "no_aws_class", "type" => "init"]]));
           }
        }
        try {
          $this->client = Aws\S3\S3Client::factory(array(
            'region' => $region,
            'version' => 'latest',
            'endpoint' => $endpoint,
            'credentials' => array(
                      'key'    => $access_key,
                      'secret' => $secret_key,
                  ),
            'bucket_endpoint' => true,
          ));
        } catch (\Exception $e) {
          $this->HandleAWSException($e);
        }
        $this->space = $spaceName;
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->host = $host;
        $this->region = $region;
    }


    /*
      Creates a Space.
    */
    function CreateSpace($spaceName, $region = "") {
        if(empty($region)) {
          $region = $this->region;
        }
        $current_space = $this->space;
        try {
          $this->SetSpace($spaceName);
          $success = $this->client->createBucket(array('Bucket' => $spaceName));
          $this->client->waitUntil('BucketExists', array('Bucket' => $spaceName));
          $this->SetSpace($current_space);
          return $this->ObjReturn($success->toArray());
        } catch (\Exception $e) {
          $this->HandleAWSException($e);
        }
    }

    /*
      Lists all spaces owned by you in the region.
    */
    function ListSpaces() {
        $current_space = $this->space;
        try {
          $this->SetSpace(NULL);
          $spaceList = $this->client->listBuckets();
          $this->SetSpace($current_space);
          return $this->ObjReturn($spaceList->toArray());
        } catch (\Exception $e) {
          $this->HandleAWSException($e);
        }
    }

    /*
      Shorthand for SetSpace - Change your current Space, Region and/or Host.
    */
    function ChangeSpace($spaceName, $region = "", $host = "") {
      return SetSpace($spaceName, $region, $host);
    }

    /*
      Changes your current Space, Region and/or Host.
    */
    function SetSpace($spaceName, $region = "", $host = "") {
        if(empty($region)) { $region = $this->region; }
        if(empty($host)) { $host = $this->host; }
        if(!empty($spaceName)) {
          $endpoint = "https://".$spaceName.".".$region.".".$host;
          $this->space = $spaceName;
        } else {
          $endpoint = "https://".$region.".".$host;
          $this->space = "";
        }
        try {
          $this->client = Aws\S3\S3Client::factory(array(
            'region' => $region,
            'version' => 'latest',
            'endpoint' => $endpoint,
            'credentials' => array(
                      'key'    => $this->access_key,
                      'secret' => $this->secret_key,
                  ),
            'bucket_endpoint' => true
          ));
          return $this->ObjReturn(true);
        } catch (\Exception $e) {
          $this->HandleAWSException($e);
        }
    }

    /*
      Fetches the current Space's name.
    */
    function GetSpaceName() {
        return $this->ObjReturn($this->space);
    }


    /*
      Downloads the whole Space to a directory.
    */
    function DownloadSpaceToDirectory($pathToDirectory) {
      try {
        $this->client->downloadBucket($pathToDirectory, $this->space);
        return $this->ObjReturn(true);
      } catch (\Exception $e) {
        $this->HandleAWSException($e);
      }
    }

    /*
      Deletes the current Space.
    */
    function DestroyThisSpace() {
        try {
          $objects = $this->ListObjects();
          foreach ($objects as $value) {
            $key = $value["Key"];
            $this->DeleteObject($key);
          }
          $this->client->deleteBucket(array('Bucket' => $this->space));
          $this->client->waitUntil('BucketNotExists', array('Bucket' => $this->space));
         return $this->ObjReturn(true);
         }
         catch (\Exception $e) {
          $this->HandleAWSException($e);
         }
    }




    /*
      Lists all objects.
    */
    function ListObjects($of_directory = "") {
      try {
         $objects = $this->client->getIterator('ListObjects', array(
             'Bucket' => $this->space,
             "Prefix" => $of_directory,
         ));
         $objectArray = array();
         foreach ($objects as $object) {
           $objectArray[] = $object;
         }
         return $this->ObjReturn($objectArray);
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Checks whether an object exists.
    */
    function DoesObjectExist($objectName) {
      try {
         return $this->ObjReturn($this->client->doesObjectExist($this->space, $objectName));
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Fetches an object's details.
    */
    function GetObject($file_name = "") {
      try {
        $result = $this->client->getObject([
          'Bucket' => $this->space,
          'Key' => $file_name,
         ]);
         return $this->ObjReturn($result->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Makes an object private, (restricted) access.
    */
    function MakePrivate($file_path = "") {
      try {
        return $this->PutObjectACL($file_path, ["ACL" => "private"]);
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Makes an object public anyone can access.
    */
    function MakePublic($file_path = "") {
      try {
        return $this->PutObjectACL($file_path, ["ACL" => "public-read"]);
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Deletes an object.
    */
    function DeleteObject($file_path = "") {
      try {
        return $this->ObjReturn($this->client->deleteObject([
        'Bucket' => $this->space,
        'Key' => $file_path,
        ])->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Upload a file.
    */
    function UploadFile($pathToFile, $access = "private", $save_as = "") {
        if(empty($save_as)) {
          $save_as = $pathToFile;
        }
        if($access == "public") {
          $access = "public-read";
        }
        if(!is_file($pathToFile)){
          $file = $pathToFile;
        }else{
          $file = fopen($pathToFile, 'r+');
        }
        try {
          $result = $this->client->putObject(array(
              'Bucket'  => $this->space,
              'Key'     => $save_as,
              'Body'    => $file,
              "ACL"     => $access,
              'CacheControl' => 'max-age=3153600',
          ));

          $this->client->waitUntil('ObjectExists', array(
              'Bucket' => $this->space,
              'Key'    => $save_as
          ));

          return $this->ObjReturn($result->toArray());
         }
         catch (\Exception $e) {
          $this->HandleAWSException($e);
         } finally {     
            if (is_file($pathToFile)) {
                fclose($file);
            }
         }
    }

    /*
      Download a file.
    */
    function DownloadFile($fileName, $destinationPath = false) {
      try {
        if(!$destinationPath) {
              $result = $this->client->getObject(array(
                  'Bucket' => $this->space,
                  'Key'    => $fileName,
              ));

              return $result['Body'];
          }else{
              $result = $this->client->getObject(array(
                  'Bucket' => $this->space,
                  'Key'    => $fileName,
                  'SaveAs' => $destinationPath
              ));

              return $this->ObjReturn($result->toArray());
          }
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Uploads all contents of a directory.
    */
    function UploadDirectory($directory, $keyPrefix = "") {
      try {
        return $this->client->uploadDirectory($directory, $this->space, $keyPrefix);
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }





    /*
      Lists the CORS policy of the Space.
    */
    function ListCORS() {
      try {
        $cors = $this->client->getBucketCors([
          'Bucket' => $this->space,
         ]);
         return $this->ObjReturn($cors->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Updates the CORS policy of the Space.
    */
    function PutCORS($cors_rules = "") {
      if(empty($cors_rules)) {
        $cors_rules = [
         'AllowedMethods' => ['GET'],
         'AllowedOrigins' => ['*'],
         'ExposeHeaders' => ['Access-Control-Allow-Origin'],
         ];
        }
        try {
          $result = $this->client->putBucketCors([
            'Bucket' => $this->space,
            'CORSConfiguration' => ['CORSRules' => [$cors_rules]]
          ]);
          return $this->ObjReturn($result->toArray());
         }
         catch (\Exception $e) {
          $this->HandleAWSException($e);
         }
    }

    /*
      Fetches the ACL (Access Control Lists) of the Space.
    */
    function ListSpaceACL() {
      try {
        $acl = $this->client->getBucketAcl([
          'Bucket' => $this->space,
         ]);
        return $this->ObjReturn($acl->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Updates the ACL (Access Control Lists) of the Space.
    */
    function PutSpaceACL($params) {
      try {
        $acl = $this->client->putBucketAcl($params);
        return $this->ObjReturn($acl->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Lists an object's ACL (Access Control Lists).
    */
    function ListObjectACL($file) {
      try {
        $result = $this->client->getObjectAcl([
           'Bucket' => $this->space,
           'Key' => $file,
        ]);
        return $this->ObjReturn($result->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }

    /*
      Updates an object's ACL (Access Control Lists).
    */
    function PutObjectACL($file, $acl) {
      try {
        $acl = array_merge(array("Bucket" => $this->space, "Key" => $file), $acl);
        $result = $this->client->putObjectAcl($acl);
        return $this->ObjReturn($result->toArray());
       }
       catch (\Exception $e) {
        $this->HandleAWSException($e);
       }
    }



    /*
      Creates a temporary URL for a file (Mainly for accessing private files).
    */
    function CreateTemporaryURL($file_name = "", $valid_for = "1 hour") {
        $secret_key = $this->secret_key;
        $expiry = strtotime("+ ".$valid_for);
        $file_name = rawurlencode($file_name);
        $file = str_replace(array('%2F', '%2B'), array('/', '+'), ltrim($file_name, '/') );
        $objectPathForSignature = '/'. $this->space .'/'. $file_name;
        $stringToSign = implode("\n", $pieces = array('GET', null, null, $expiry, $objectPathForSignature));
        $url = 'https://' . $this->space . '.'.$this->region.'.'.$this->host.'/' . $file_name;
        $blocksize = 64;
        if (strlen($secret_key) > $blocksize) $secret_key = pack('H*', sha1($secret_key));
        $secret_key = str_pad($secret_key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack( 'H*', sha1(($secret_key ^ $opad) . pack( 'H*', sha1(($secret_key ^ $ipad) . $stringToSign))));
        $signature = base64_encode($hmac);
        $queries = http_build_query($pieces = array(
                  'AWSAccessKeyId' => $this->access_key,
                  'Expires' => $expiry,
                  'Signature' => $signature,
                 ));
       $url .= "?".$queries;
       return $this->ObjReturn($url);
    }


    /*
      INTERNAL FUNCTION - Returns a standardized object.
    */
    function ObjReturn($return) {
      $return = @json_decode(@json_encode($return), true);
      $return = $this->AWSTime($return);
      return $return;
    }

    /*
      INTERNAL FUNCTION - Converts all AWS time values to unix timestamps.
    */
    function AWSTime($obj) {
      $time_keys = ["LastModified", "CreationDate", "Expires", "last-modified", "date", "Expiration"];
      if(is_array($obj)) {
        foreach ($obj as $key => $value) {
          if(is_array($obj[$key])) {
            $obj[$key] = $this->AWSTime($obj[$key]);
          }
          else {
            foreach ($time_keys as $time_key) {
              if(array_key_exists($time_key, $obj) && !empty($obj[$time_key]) && !is_numeric($obj[$time_key])) {
                  $obj[$time_key] = strtotime($obj[$time_key]);
              }
            }
          }
        }
      }
      return $obj;
    }

    /*
      INTERNAL FUNCTION - Checks whether an array has any keys.
    */
    private function any_key_exists($keys, $arr) {
      foreach ($keys as $key) {
        if(array_key_exists($key, $arr)) {
          return true;
        }
      }
      return false;
    }

    /*
      INTERNAL FUNCTION - Standardizes AWS errors.
    */
    function HandleAWSException($e) {
      if(get_class($e) == "Aws\S3\Exception\AwsException") {
        $error["error"] = [
          "message" => $e->getAwsErrorMessage(),
          "code" => $e->getAwsErrorCode(),
          "type" => $e->getAwsErrorType(),
          "http_code" => $e->getStatusCode(),
        ];
       }
      else {
        throw $e;
      }
      throw new SpacesAPIException(@json_encode($error));
    }

}

/*
  INTERNAL FUNCTION - Throws error for catching.
*/
class SpacesAPIException extends \Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function GetError() {
      $error = @json_decode($this->getMessage(), true);
      return $error["error"];
    }
}
