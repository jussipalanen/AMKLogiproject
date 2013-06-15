<?php
$getUsername    = Yii::app()->getRequest()->getQuery('username');
$uid            = User::model()->getProfileUIDByUsername($getUsername);
$profileinfo    = Profile::model()->find("uid = '" . $uid . "'");
?>
<ul>
    <li><?php echo $profileinfo->firstname;?></li>
    <li><?php echo $profileinfo->surname;?></li>
    <li><?php echo $profileinfo->gender;?></li>
    <li><?php echo $profileinfo->birthday;?></li>
    <li><?php echo $profileinfo->email;?></li>
    <li><?php echo $profileinfo->city;?></li>
    <li><?php echo $profileinfo->relationship;?></li>
    <li><?php echo $profileinfo->homepage;?></li>
    <li><?php echo $profileinfo->skype;?></li>
</ul>
<?php echo "<img src='" . $profileinfo->personal_image_url . "'>";