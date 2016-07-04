# Behat Shortcode Extension

## Description

This extension was created to provide a transparent way to feed runtime 
values into a feature.

For example my last use-case was verifying a sitemap:
 
```
  Scenario: Sitemap contains homepage
    When the sitemap is rendered
    Then the sitemap file contains:
      |<url><loc>https://example.com/</loc><lastmod>[date type=now format=Y-m-d]</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>|
``` 
 
The extension will replace the shortcode `[date type=now format=Y-m-d]` 
with the current date so the step code can perform a direct comparison. 

 

## Considerations

You should always seek to avoid having to use this extension!

Firstly, it should be possible to abstract runtime values into the step 
methods directly, without exposing them in the feature. The above example 
could easily be rewritten as: 

```
  Scenario: Sitemap contains homepage
    When the sitemap is rendered
    Then the sitemap file contains:
      | url                  | frequency | priority |
      | https://example.com/ | daily     | 1.0      |
```

Secondly runtimes values should be sought to be fixed by utilising doubles
or mock services for your tests. Then runtime variables can be set in
your 'given' steps:

```
  Scenario: Sitemap contains homepage
    Given the date is "2016-07-03"
    When the sitemap is rendered
    Then the sitemap file contains:
      |<url><loc>https://example.com/</loc><lastmod>2016-07-03</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>|
```

Finally this extension uses reflection to inject the shortcode before the 
step method is called, which always feels wrong.


However, sometimes it's not always possible or practical to follow the 
perfect path and it can on occasions make features easier to read and step
methods cleaner. Particularly when used in tables.


## Installation

Install via composer:

```
composer require --dev rocketage/behat-shortcode-extension
```

Then add the extension to your behat.yml file:

```
default:
  suites:
    default:
      contexts:
        - FeatureContext
  extensions:
    Rocketage\Behat\ShortcodeExtension:
    
```


## Configuration

The extension currently only ships with a datetime shortcode, which 
 effectively wraps the DateTime php class. The shortcode uses the 
 following format `[date type=now format=Y-m-d zone=UTC]`. Because it uses
 DateTime you can use all the friendly contructor strings in the `type` 
 field like 'yesterday', 'last week', 'next year', etc.
 
You can add more shortcodes by supplying classnames in the behat.yml file:
 
```
default:
  suites:
    default:
      contexts:
        - FeatureContext
  extensions:
    Rocketage\Behat\ShortcodeExtension:
      shortcodes:
        - MyProject\CustomShortcode1
        - MyProject\CustomShortcode2
        
``` 
 
The custom classes must return a SimpleShortCode instance via the __invoke 
 magic method:
 
```
<?php

namespace MyProject;

use Maiorano\Shortcodes\Library\SimpleShortcode;

class CustomShortcode1
{
    public function __invoke()
    {
        return new SimpleShortcode(
            'hostname',
            null,
            /* $content is the string between shortcodes, $atts are parameters */
            function($content = null, array $atts = []) {
                return gethostname();
            }
        );
    }
} 
``` 
 
