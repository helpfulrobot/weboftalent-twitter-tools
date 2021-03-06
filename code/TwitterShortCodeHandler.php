<?php

class TwitterShortCodeHandler {

    // taken from http://www.ssbits.com/tutorials/2010/2-4-using-short-codes-to-embed-a-youtube-video/ and adapted for SS3
    public static function parse_tweet( $arguments, $caption = null, $parser = null ) {
        if(!isset($arguments['id'])){
            return null;
        }
        if(substr($arguments['id'], 0, 4) == 'http'){
            $id = explode('/status/', $arguments['id']);
            $id = $id[1];
        }
        else{
            $id = $arguments['id'];
        }

        $tweet = EmbeddedTweet::get()->filter('TwitterID',$id)->first();
        if (!$tweet) {
            $data = json_decode(file_get_contents('https://api.twitter.com/1/statuses/oembed.json?align=center&id='.$id.'&omit_script=true'), 1);
            $tweet = new EmbeddedTweet();
            $tweet->URL = $data['url'];
            $tweet->TwitterID = $id;
            $tweet->HTML = $data['html'];

            $author = EmbeddedTweetAuthor::get()->filter('Name', $data['author_name'])->first();
            if (!$author) {
                $author = new EmbeddedTweetAuthor();
                $author->Name = $data['author_name'];
                $author->URL = $data['author_url'];
                $author->write();
            }

            $tweet->Author = $author;
            $tweet->write();

        }
        

        return $tweet->HTML;
        /*

            'cache_age' => 'Int',
        'URL' => 'Varchar(255)',
        'TwitterID' => 'VarChar(40)',
        'HTML' => 'Text'


        //set dimensions
        $customise['width'] = 640;
        $customise['height'] = 385;

        //overide the defaults with the arguments supplied
        $customise = array_merge( $customise, $arguments );

        //get our YouTube template
        $template = new SSViewer( 'Twitter' );

        //return the customised template
        return $template->process( new ArrayData( $customise ) );
        */
    }
}
?>