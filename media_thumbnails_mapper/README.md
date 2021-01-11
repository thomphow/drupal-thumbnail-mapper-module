# Media Thumbnails Mapper module

This module uses the [Media Thumbnails](https://www.drupal.org/project/media_thumbnails) framework
to create media entity thumbnails for files.

“Media Thumbnails” and “Media Thumbnails PDF” modules do work but use ImageMagick locally. Not all 
shared server space allows for it to be installed and there were issues with the version of it that 
would work with Drupal 8/Centos 8. 

This Thumbnails Mapper Module allows you to create thumbnails from other images already loaded in Drupal. 
So, if you had pineapple.jpg, then it could be used to automatically generate a thumbnail for pineapple.pdf. 

Maintenance: This is not a formally submitted or maintained Drupal Module. 

It hasn't been submitted for two reasons. 
1. We had a conflict between the Drupal Feeds module and the Media Thumbnails module (not ours). When we 
chose "Delete Items" that had previously been imported by a Feed, it would fail with an error. Removing
the Media Thumbnails module solved this problem. We didn't end up using any form of Thumbnails due to this problem. In the end, we didn't use the Thumbnails Mapper module. 
2. The PHP code was written quickly. It still has debug/commented out statements. It is not very 
efficient code. If you are making mappings of hundreds of images and pdf files, you might see an impact. It should possibly be
rewritten, caching file names to a file. 

