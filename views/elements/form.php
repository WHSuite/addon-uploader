<?php echo $assets->script('uploader::jquery.cloneable.js'); ?>
<?php echo $assets->style('uploader::uploader-form.css'); ?>

<div class="cloneable uploader">

    <?php if (isset($model_id) && ! empty($model_id)): ?>
        <h4><?php echo $lang->get('existing_files'); ?></h4>

        <?php
            // load the uploader plugin form
            echo $this->fetch(
                'uploader::elements/listing.php',
                array(
                    'model_name' => $model_name,
                    'model_id' => $model_id,
                    'admin' => (isset($admin) ? $admin : false)
                )
            );
        ?>
    <?php endif; ?>

    <h4><?php echo $lang->get('attach_files'); ?></h4>

    <div class="clone-row disabled-row">

        <?php
            echo $forms->file(
                'data.' . $model_name . '.0.filename',
                $lang->get('choose_file'),
                array(
                    'disabled' => 'disabled'
                )
            );
        ?>

        <a href="" class="delete-item">
            <?php echo $lang->get('delete'); ?>
        </a>
    </div>

    <a href="" class="add-item">
        <?php echo $lang->get('add_new_file'); ?>
    </a>

</div>

<script>

    $(document).ready(function() {

        $('.cloneable').cloneable();

    });

</script>