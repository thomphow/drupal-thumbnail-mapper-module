<?php

namespace Drupal\media_thumbnails_mapper\Plugin\MediaThumbnail;

use Drupal\Core\Entity\EntityStorageException;
use Drupal\file\Entity\File;
use Drupal\media_thumbnails\Plugin\MediaThumbnailBase;

/**
 * Media thumbnail plugin for pdf documents.
 *
 * @MediaThumbnail(
 *   id = "media_thumbnail_mapper",
 *   label = @Translation("Media Thumbnail Mapper"),
 *   mime = {
 *     "application/pdf",
 *   }
 * )
 */


/* Warning: This was put togehter in a short time. It is shamefully rediculously inefficient.
* Multiple folders are search for each attempt to match a pdf file with a jpg file.
* It should be re-written to cache the file names into a Key(file name) Value (file name with full path)
* pair list that can be efficiently searched.
*
* Problems include clean up for such a cached structure; we don't know the number of calls to
* createThumbnail. Could just use a temporary file, might still be better than these folder searches.
*/

class MediaThumbnailsMapper extends MediaThumbnailBase {

  // public function rsearch($folder, $pattern) {
  //   $dir = new \RecursiveDirectoryIterator($folder);
  //   $ite = new \RecursiveIteratorIterator($dir);

  //   $files = new \RegexIterator($ite, $pattern, \RegexIterator::GET_MATCH);
  //   // $this->logger->warning($this->t('Thumbnail Create - rsearch dir[' . $dir . '] pattern [' . $pattern . ']'));

  //   $fileList = array();
  //   foreach($files as $file) {
  //     $this->logger->warning($this->t('Thumbnail Create - rsearch add file [' . $file . ']'));

  //       $fileList = array_merge($fileList, $file);
  //   }
  //   return $fileList;
  // }


  public function fileSearch($folder, $name) {
    $fileList = array();
    $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($folder));
    foreach ($dir as $file) {

      // styles folder has the actual generated thumbnails in them, with the same file name...not wanted
      if (strpos($file, '/styles/') == false) {
        $filename = pathinfo($file, PATHINFO_BASENAME);
        if ($filename == $name) {
          $this->logger->warning($this->t('Thumbnail Create - found [' . $file . ']'));
          $fileList = array_merge($fileList, array($file));
        }
      }
    }
    return $fileList;
  }

  /**
   * Creates a managed thumbnail file using the passed source file uri.
   *
   * {@inheritdoc}
   */
  public function createThumbnail($sourceUri) {


    $this->logger->warning($this->t('Thumbnail Create - Source Uri[' . $sourceUri . ']'));

    $path = $this->fileSystem->realpath($sourceUri);
    if (!file_exists($path)) {
      $this->logger->warning($this->t('Thumbnail Create - file exist failure [' . $sourceUri . ']'));
        return NULL;
    }

    $dir = pathinfo($path, PATHINFO_DIRNAME);
    $without_extension = pathinfo($path, PATHINFO_FILENAME);

    $parent = pathinfo($dir, PATHINFO_DIRNAME);

    // $this->logger->warning($this->t('Thumbnail Create - Search [' . $parent . '][' . $without_extension . '.jpg]'));


    //     ^ is the regular expression denoting the beginning of the string,
    // \\ escapes the . to make it a literal .,
    // $ is the regular expression denoting the end of the string.
    // Apparently delimiters are needed .... $numpattern="/^([0-9]+)$/";

    // Sample call - $img360 = count($this->rSearch($dst, '/\.(jpeg|jpg|png|gif|bmp|tif|tiff)/i'))/2;

    // $result_array  = $this->rsearch($parent, '/^' . $without_extension . '\.jpg$/');
    $result_array  = $this->fileSearch($parent, $without_extension . '.jpg');

    if (count($result_array) == 0) return NULL;
    // $this->logger->warning($this->t('Thumbnail Create - Search Result Count[' . count($result_array) . ']'));

    if (!$result_array[0]) return NULL;

    $local_uri = $result_array[0];

    // $this->logger->warning($this->t('Thumbnail Create - Search Results[' . $local_uri . ']'));

    // $this->logger->warning($this->t('Thumbnail Create - file_get_contents attempt [' . $local_uri . ']'));

    $image_data = file_get_contents($local_uri);
    if (!$image_data) {
      // $this->logger->warning($this->t('Thumbnail Create - file_get_contents failure [' . $local_uri . ']'));
      return NULL;
    }

    // $this->logger->warning($this->t('Thumbnail Create - file save data attempt [' . $sourceUri . '.jpg' . ']'));
    return file_save_data($image_data, $sourceUri . '.jpg');

  }

}
