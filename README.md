# wp-posts-api

Basic API to provide recent post previews as JSON.

## Latest posts

Returns the 10 latest posts, oldest first.

```markup
http://example.com/?gwasw_api=1
```

### Response:

```json
{
	"posts": [{
		"id": 1,
		"guid": "http:\/\/example.com\/?p=1",
		"title": "Hello world!",
		"excerpt": "Welcome to WordPress. This is your first post. Edit or delete it, then start writing!",
		"published_gmt": "2017-03-19 06:33:26",
		"url": "http:\/\/example.com\/2017\/03\/hello-world\/",
		"imageurl": "http:\/\/localhost\/wp_test\/wp-content\/uploads\/2017\/03\/photo.jpeg",
		"tags": ["mytag"]
	}, {
		...
	}]
}
```

## Posts since ID

Returns the 10 posts newer than the post with the ID passed, oldest first.

```markup
http://example.com/?gwasw_api=1&idpost=[integer]
```

### Response:

See above.

### Errors:

```json
{
	"error": "post does not exist"
}
```

## Single post

Returns a single post:

```markup
http://example.com/?gwasw_api=1&idsince=[integer]
```

### Response:

```json
{
	"post": {
		"id": 1,
		"guid": "http:\/\/example.com\/?p=1",
		"title": "Hello world!",
		"excerpt": "Welcome to WordPress. This is your first post. Edit or delete it, then start writing!",
		"published_gmt": "2017-03-19 06:33:26",
		"url": "http:\/\/example.com\/2017\/03\/hello-world\/",
		"imageurl": "http:\/\/localhost\/wp_test\/wp-content\/uploads\/2017\/03\/photo.jpeg",
		"tags": ["mytag"]
	}
}
```

### Errors:

```json
{
	"error": "post does not exist"
}
```
