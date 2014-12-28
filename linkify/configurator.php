<form class="form-plugin" action="<?php echo $config->url_current; ?>/update" method="post">
  <input name="token" type="hidden" value="<?php $linkify_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); echo $token; ?>">
  <div class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->scope; ?></span>
    <span class="grid span-5">
    <?php

    $options = array(
        'article:content' => $speak->articles,
        'page:content' => $speak->pages,
        'comment:message' => $speak->comments
    );

    foreach($options as $hook => $label) {
        echo '<div><label><input type="checkbox" name="scopes[]" value="' . $hook . '"' . (in_array($hook, $linkify_config['scopes']) ? ' checked' : "") . '> <span>' . $label . '</span></label></div>';
    }

    ?>
    </span>
  </div>
  <div class="grid-group">
    <span class="grid span-1"></span>
    <span class="grid span-5"><button class="btn btn-action" type="submit"><i class="fa fa-check-circle"></i> <?php echo $speak->update; ?></button></span>
  </div>
</form>