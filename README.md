**Sample Url Structure** 

    http://test.local:8001/shukran/testing/421/2520-x-928.jpg?size=800x450

 - http://test.local:8001 => image resize service base url
 - shukran => app name (config files are stored based on this and each app can have its own config)
 - testing/421/2520-x-928.jpg => actual file path
 - size=800x450  => size identifier ( now size is dynamic and can  generate thumbnails of any size)

> Note: All the requests should be routed through index.php







