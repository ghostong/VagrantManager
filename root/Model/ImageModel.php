<?php

class ImageModel extends \Lit\LitMs\LitMsModel {

    function imageList(){
        if(!is_dir($this->getBoxDir())){
            return [];
        }
        $fileIterator = new \FilesystemIterator( $this->getBoxDir() );
        $imageList = [];
        foreach($fileIterator as $fileInfo) {
            if(substr($fileInfo->getFilename(),0,1) == "."){
                continue;
            }
            if($fileInfo->isFile()){
                $expFile = explode("_import_",$fileInfo->getFilename());
                $imageFile = $expFile[0];
                $importName = @$expFile[1] ? : "";
                $isImport = $importName ? 1 : 0;
                $imageList[] = ["imageFile"=>$imageFile,"isImport"=>$isImport,"importName"=>$importName];
            }
        }
        return $imageList;
    }

    function imageFileRename ( $imagePath,$imageName )  {
        return rename($imagePath,$imagePath."_import_".$imageName);
    }

    function imageFileDelete ( $imagePath,$imageName ){
        if ($imageName) {
            return unlink($imagePath."_import_".$imageName);
        }else{
            return false;
        }
    }

    function getBoxDir(){
        return VAGRANT_DATA_DIR.'Box'.DIRECTORY_SEPARATOR;
    }


}