<?php $post_files = \Addon\Uploader\Libraries\Get::allFiles($model_name, $model_id); ?>

<?php if (isset($post_files) && ! empty($post_files)): ?>
    <?php echo $assets->style('uploader::uploader-listing.css'); ?>

    <ul class="files">

        <?php foreach ($post_files as $file): ?>

            <?php
                $filename = \Addon\Uploader\Libraries\Process::filename($file->getFilename());
            ?>
            <li>
                <a href="<?php echo $router->generate('admin-uploader-download', array(
                    'model_name' => $model_name,
                    'model_id' => $model_id,
                    'filename' => $file->getFilename()
                )); ?>">
                    <?php echo $filename; ?>
                </a>

                <?php if (isset($admin) && $admin): ?>

                    <a href="<?php echo $router->generate('admin-uploader-ajax-delete', array(
                        'model_name' => $model_name,
                        'model_id' => $model_id,
                        'filename' => $file->getFilename()
                    )); ?>" class="ajax-delete">
                        <?php echo $lang->get('delete'); ?>
                    </a>

                <?php endif; ?>
            </li>

        <?php endforeach; ?>

    </ul>

    <?php if (isset($admin) && $admin): ?>

        <?php echo $assets->script('uploader::ajax-delete.js'); ?>

    <?php endif; ?>

<?php endif; ?>
