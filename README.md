MTIF Parser
===

What is It?
---
MTIF Parser is a collection of classes to easily parse Moveable Type export data.  Since MTIF data dumps can be extremely large, the classes iterate over export text one record at a time to keep memory use manageable.

MTIF Parser was originally a part of [TypePad2WordPress](https://tp2wp.com), but has been broken out to make it easier to convert from moveable type to other data representations.


Using MITF Parser
---
Its dead simple.

    $parser = new \PES\MTIF\Parser();
    $parser->setMTIFPath('./path/to/data.txt');

    while ($post = $parser->next()) {

        // each $post here is an instantiated PES_MTIF_Post object,
        // that encapsulates data about each post, including its title,
        // original URL, comments, etc

        echo 'Post title: ' . $post->title() . ' by ' . $post->author() . "\n";
    }


More Info
---
Pete Snyder &lt;snyderp@gmail.com&gt; wrote this in 2012 from an apartment room w/ one month left on the lease
