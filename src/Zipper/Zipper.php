<?php
namespace RudyMas\Zipper;

use ZipArchive;

/**
 * Class Zipper
 * Zip/Unzip Files or Folders
 *
 * @author      Rudy Mas <rudy.mas@rudymas.be>
 * @copyright   2013 - 2016, rudymas.be. (http://www.rudymas.be/)
 * @license     https://opensource.org/licenses/GPL-3.0 GNU General Public License, version 3 (GPL-3.0)
 * @version     1.2.2
 * @package     RudyMas\Zipper
 */
class Zipper extends ZipArchive
{
    /**
     * @var string $openFile
     */
    private $openFile;

    /**
     * Zipper constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        $this->openFile = $file;
        if (is_file($file)) {
            $testZip = @parent::open($file);
            if ($testZip !== TRUE) $this->error($testZip);
        } else {
            $testZip = @parent::open($file, parent::CREATE);
            if ($testZip !== TRUE) $this->error($testZip);
        }
    }

    /**
     * function close()
     * Closing zip-file
     */
    public function close()
    {
        @parent::close() or die("There was an error while closing: '$this->openFile'.");
    }

    /**
     * function unZip($targetFolder)
     * Unzipping to a specific folder
     * @param string $targetFolder The folder to extract to
     */
    public function unZip($targetFolder)
    {
        if (substr($targetFolder, -1) != '/') $targetFolder = $targetFolder . '/';
        @parent::extractTo($targetFolder) or die("There was an error while unzipping: '$this->openFile'.");
    }

    /**
     * function zipThisFile($folder, $fileToZip)
     * Zipping a specific file in a specific folder to the zip-file
     * @param string $folder The folder where the file exists
     * @param string $fileToZip The file to be zipped
     */
    public function zipThisFile($folder, $fileToZip)
    {
        $map = opendir($folder);
        while ($file = readdir($map)) {
            if ($file != '.' && $file != '..' && $file == $fileToZip) {
                if (is_file("$folder/$file")) @parent::addFile("$folder/$file", $file) or die("'$file' couldn't be added to '$this->openFile'.");
            }
        }
        closedir($map);
    }

    /**
     * function zipAllFilesInFolder($folder)
     * Zipping all files in a specific folder to the zip-file
     * @param string $folder The folder where the files exists
     */
    public function zipAllFilesInFolder($folder)
    {
        $map = opendir($folder);
        while ($file = readdir($map)) {
            if ($file != '.' && $file != '..') {
                if (is_file("$folder/$file")) @parent::addFile("$folder/$file", $file) or die("'$file' couldn't be added to '$this->openFile'.");
            }
        }
        closedir($map);
    }

    /**
     * function zipAllFilesAndFoldersInFolder($folder)
     * Zipping all files and folders in a specific folder to the zip-file
     * @param string $folder The folder where the files and folders exists
     */
    public function zipAllFilesAndFoldersInFolder($folder)
    {
        $map = opendir($folder);
        while ($file = readdir($map)) {
            if ($file != '.' && $file != '..') {
                if (is_file("$folder/$file")) {
                    @parent::addFile("$folder/$file") or die("'$folder/$file' couldn't be added to '$this->openFile'.");
                }
                if (is_dir("$folder/$file")) {
                    @parent::addEmptyDir("$folder/$file") or die("The folder '$folder/$file' couldn't be added to '$this->openFile'.");
                    $this->zipAllFilesAndFoldersInFolder("$folder/$file");
                }
            }
        }
        closedir($map);
    }

    /**
     * function error($response)
     * To show the kind of error ZipArchive returned
     * @param string $response
     */
    private function error($response)
    {
        switch ($response) {
            case parent::ER_EXISTS:
                $ErrMsg = "File already exists.";
                break;
            case parent::ER_INCONS:
                $ErrMsg = "Zip archive inconsistent.";
                break;
            case parent::ER_MEMORY:
                $ErrMsg = "Memory failure.";
                break;
            case parent::ER_NOENT:
                $ErrMsg = "No such file.";
                break;
            case parent::ER_NOZIP:
                $ErrMsg = "Not a zip archive.";
                break;
            case parent::ER_OPEN:
                $ErrMsg = "Can't open file.";
                break;
            case parent::ER_READ:
                $ErrMsg = "Read error.";
                break;
            case parent::ER_SEEK:
                $ErrMsg = "Seek error.";
                break;
            default:
                $ErrMsg = "Unknow (Code $response)";
        }
        die("Zipper Error: $ErrMsg");
    }
}
/** End of File: Zipper.php **/