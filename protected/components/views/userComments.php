<?php
$getUsername     = Yii::app()->getRequest()->getQuery('username');
$model           = new User();
$to_uid          = $model->getProfileUIDByUsername($getUsername);
$from_uid        = Yii::app()->user->id;
$model->readComments($to_uid);
?>

<ul id="comments">
    <?php
        $model->getUserComments($from_uid, $to_uid);
    ?>
</ul>
<?php echo CHtml::hiddenField('from_uid', $from_uid);?>
<?php echo CHtml::hiddenField('to_uid', $to_uid);?>
<div style="border:1px solid #000;padding:5px;" id="comment-box">
    <?php echo CHtml::textField('text-comment')?>
    <?php echo CHtml::button('Kommentoi', array('id'=>'send-comment')); ?>
    <?php echo CHtml::checkBox('isprivate', false)?>Yksityinen
    <div id="loading-comment">
           <img src="./images/ajax-loader.gif" /> <br/>Lähetetään kommentti...
    </div>
</div>
<script type="text/javascript">
/*<![CDATA[*/

$(document).ready(function() {
    $("#loading-comment").hide();
});

jQuery(function($)
{
        $('#send-comment').click(function(){
            sendComment();
            $("#text-comment").hide();
            $("#send-comment").hide();
            $('#isprivate').hide();
        });
});

function sendComment()
{
    $("#loading-comment").show();
    var comment = null;
    var from_uid = null;
    var to_uid = null;
    var isprivate = null;
    
    if($('#isprivate').is(':checked'))
    {
        isprivate = true;
    }
    else
    {
        isprivate = false;
    }
    
    comment  = $('#text-comment').val();
    to_uid   = $('#to_uid').val();
    from_uid = $('#from_uid').val();
    
    $.ajax({
        type: 'POST',
        url: 'http://localhost/amklogi/user/comment',
        data: "comment="+comment+"&from_uid="+from_uid+"&to_uid="+to_uid+"&isprivate="+isprivate,
        success:function(data){
            $("#comments").html(data);
            $("#text-comment").val("");
            $("#text-comment").show();
            $("#send-comment").show();
            $('#isprivate').show();
            $("#loading-comment").hide();
        }
    });
}
/*]]>*/
</script>