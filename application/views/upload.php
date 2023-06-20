<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="<?php echo base_url('assets/styles.css'); ?>" rel="stylesheet">
    <title>Upload Files</title>
</head>
<body>
    <div class="main">
        <div class="navigation">
            <div class="wrapper">
                <nav class="nav">
                    <h1 class="big-heading">Files Upload</h1>
                    <ul>
                        <li class="link"><a href="">Extensions</a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="wrapper">
            <form class="form" action="<?php echo base_url('Upload/import')?>" method="POST" enctype="multipart/form-data">
                <div class="input">
                    <label for="fileInput" class="label">Select a file for import</label>
                    <input type="file" class="input-import" name="upload_file" id="upload_file" size="60"/>
                </div>
                <div class="action">
                    <button type="submit" class="btn-import" name="importSubmit">Import</button>
                </div>
            </form>

            <?php if ($this->session->flashdata("success")): ?>
                <div class="alert alert-green">Database succesfully updated</div> 
            <?php elseif ($this->session->flashdata("error")): ?>
                <div class="alert alert-red">Database did not update</div>
            <?php endif; ?>
        </div>

    </div>
    
</body>
</html>

