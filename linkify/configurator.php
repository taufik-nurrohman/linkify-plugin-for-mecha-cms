<form class="form-plugin" action="<?php echo $config->url_current; ?>/update" method="post">
  <?php $linkify_config = File::open(PLUGIN . DS . basename(__DIR__) . DS . 'states' . DS . 'config.txt')->unserialize(); ?>
  <?php echo Form::hidden('token', $token); ?>
  <div class="grid-group">
    <span class="grid span-1 form-label"><?php echo $speak->scope; ?></span>
    <div class="grid span-5">
    <?php

    $options = array(
        'article:content' => $speak->articles,
        'page:content' => $speak->pages,
        'comment:message' => $speak->comments
    );

    foreach($options as $k => $v) {
        echo '<div>' . Form::checkbox('scopes[]', $k, in_array($k, $linkify_config['scopes']), $v) . '</div>';
    }

    ?>
    </div>
  </div>
  <div class="grid-group">
    <span class="grid span-1"></span>
    <span class="grid span-5"><?php echo Jot::button('action', $speak->update); ?></span>
  </div>
</form>