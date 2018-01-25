# img
For testing purposes, I have uploaded the script on my server.
 
URL: http://image-resize.ciprianspiridon.com/index.php?path=$path_of_the_file_to_load&height=$height&width=$width

```php 
$path_of_the_file_to_load = [
                "images/1.jpg",
                "images/2.jpg",
                "images/3.jpg",
                "images/4.jpg",
                "images/5.jpg",
                "images/6.jpg",
                "images/7.jpg",
                "images/8.jpg"
];
 ```
```php
$height = should be a number;
```
```php
$width = should be a number;
```
 
Example:
 - http://image-resize.ciprianspiridon.com/index.php?path=images/4.jpg&height=500&width=500
 - http://image-resize.ciprianspiridon.com/index.php?path=images/2.jpg&height=200&width=200