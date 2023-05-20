<div class="row">
    <div class="col">
        <form method="post">
            <div class="row">
                <div class="col">
                    <h1 class="h3 mb-3 fw-normal">Search photos</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg">
                    <div class="form-group">
                    <label for="rover_name">Rover</label>
                    <select name="rover_name" id="rover_name" class="form-control form-control-sm">
                        <?php
                        foreach($this->rovers as $name => $description) {
                            if($this->rover_name == $name)
                                echo '<option value="'.$name.'" selected="selected">'.$description.'</option>';
                            else
                                echo '<option value="'.$name.'">'.$description.'</option>';
                        }
                        ?>
                    </select>
                    </div>
                </div>

                <div class="col-lg">
                    <div class="form-group">
                    <label for="rover_camera">Camera</label>
                    <select name="rover_camera" id="rover_camera" class="form-control form-control-sm">
                        <?php
                        foreach($this->cameras as $name => $description) {
                            if($this->rover_camera == $name)
                                echo '<option value="'.$name.'" selected="selected">'.$description.'</option>';
                            else
                                echo '<option value="'.$name.'">'.$description.'</option>';
                        }
                        ?>
                    </select>
                    </div>
                </div>

                <div class="col-lg">
                    <div class="form-group">
                    <label for="rover_sol">Sol</label>
                    <input type="number" min="0" max="10" name="rover_sol" id="rover_sol" value="<?php $this->echo('rover_sol', 0) ?>" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="col-lg">
                    <div class="form-group">
                    <button class="btn btn-primary form-control form-control-sm align-bottom" type="submit">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

    <?php
    if(!$this->results != 'NULL') {
        echo '<hr>';
        echo '<div class="row p-3">';
        echo '<div class="col">';
        echo '<h2>Search results</h2>';
        echo '</div>';
        echo '</div>';


        echo '<div class="row">';
        echo '<div class="col">';

        if(count($this->results->photos) == 0) {
            echo '<div>No results available for this combination of filters.</div>';
        }
        else
        {
            $counter = 0;
            $row_tag_closed = true;
            foreach($this->results->photos as $photo) {
                if($counter == 0) {
                    echo '<div class="row">';
                    $row_tag_closed = false;
                }
                $counter ++;

                echo '
                    <div class="col-lg-4">
                    <div class="card m-2">
                        <a href="#" data-featherlight="'.$photo->img_src.'">
                        <img class="card-img-top" src="'.$photo->img_src.'" alt="'.$photo->camera->full_name.'">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">'.$photo->camera->full_name.'</h5>
                            <p class="card-text">Date '.$photo->earth_date.'</p>
                        </div>
                    </div>
                    </div>';

                if($counter == 3) {
                    $row_tag_closed = true;
                    $counter = 0;
                    echo '</div>';
                }
            }

            if(!$row_tag_closed) echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }
    ?>

<script src="//code.jquery.com/jquery-latest.js"></script>
<script src="//cdn.jsdelivr.net/npm/featherlight@1.7.14/release/featherlight.min.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">

    function ajaxSuccess(data, status, jqXHR) {
        $('#rover_sol').attr('max', data.max_sol);
        solChanged();
    }

    function solChanged() {
        var maxValue = parseInt($('#rover_sol').attr('max'));
        var solValue = parseInt($('#rover_sol').val());

        if(solValue > maxValue)
        {
            solValue = maxValue;
            $('#rover_sol').val(solValue);
        }
    }


    function updateFields() {
        // $('.form-control').prop("disabled", true);
        $.ajax({
            type: "POST",
            url: "/?u=Search/manifest",
            data: {'rover': $('#rover_name').val()},
            success: ajaxSuccess
        });
    }

    function onChange() {
        $('#rover_name').change(function (){
            updateFields();
        });

        $('#rover_sol').change(function (){
            solChanged();
        });
    }

    $(document).ready(function() {
        updateFields();
        onChange();
    });
</script>