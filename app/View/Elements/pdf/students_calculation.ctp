<table border="1" style="width: 50%;">
    <?php 
    foreach($studentDetails as $coursesId => $count) {
    ?>
    <tr class="even">
        <td style="width: 70%;"><?php echo __('Course'); ?></td>
        <td><?php echo $courses[$coursesId]; ?></td>
    </tr>
    <tr>
        <td style="width: 70%;"><b><?php echo __('Students from this driving school'); ?></b></td>
        <td><b><?php echo isset($count['own_students']) ? $count['own_students'] : 0; ?></b></td>
    </tr>
    <tr class="">
        <td style="width: 70%;"><b><?php echo __('Students from other driving schools'); ?></b></td>
        <td><b><?php echo (isset($count['other_students'])) ? array_sum($count['other_students']) : 0; ?></b></td>
    </tr>
    <?php
    if(isset($count['other_students']) && is_array($count['other_students'])) {
        foreach($count['other_students'] as $school => $number) {
            ?>
            <tr class="">
                <td style="width: 70%;"><?php echo $drivingSchools[$school]; ?></td>
                <td><?php echo $number; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr class="">
        <td style="width: 70%;"><b><?php echo __('Own students and Other teachers'); ?></b></td>
        <td><b><?php echo (isset($count['own_students_other_teachers'])) ? array_sum($count['own_students_other_teachers']) : 0; ?></b></td>
    </tr>
    <?php 
    if(isset($count['own_students_other_teachers']) && is_array($count['own_students_other_teachers'])) {
        foreach($count['own_students_other_teachers'] as $school => $number) {
        ?>
        <tr class="">
            <td style="width: 70%;"><?php echo $drivingSchools[$school]; ?></td>
            <td><?php echo $number; ?></td>
        </tr>
        <?php
        }
    } ?>
        <tr class=""><td colspan="2">&nbsp;</td></tr>
    <?php
    }
    ?>
</table>