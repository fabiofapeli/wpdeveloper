<?php

class Supermoon_Text_Wrangler {
  function site_generator($translation, $text, $domain) {
  $translations = &get_translations_for_domain( $domain );
  if ( $text == 'Proudly powered by %s.' ) {
   return $translations->translate( 'Powered by %s</a>. <a href="http://socialmediapower.com">Supermoon theme by Social Media Power</a>.<a href="http://deltina.com"> Photos by Deltina Hay.' );
  }
  return $translation;
 }

}

?>
