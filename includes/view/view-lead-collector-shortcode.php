<?php ?>


<form class='wordpress-ajax-form-lead-collector' method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
  <div class="form-group">
    <label for="sc_name"><?php echo $a['name']; ?></label>
    <input type="text" class="form-control" id="sc_name" name="sc_name" maxlength="<?php echo $a['name_max'];?>">
  </div>
  <div class="form-group">
    <label for="sc_phone"><?php echo $a['phone']; ?></label>
    <input type="tel" class="form-control" id="sc_phone" name="sc_phone" maxlength="<?php echo $a['phone_max'];?>">
  </div>
  <div class="form-group">
    <label for="sc_email"><?php echo $a['email']; ?></label>
    <input type="email" class="form-control" id="sc_email" name="sc_email" maxlength="<?php echo $a['email_max'];?>">
  </div>
  <div class="form-group">
    <label for="sc_budget"><?php echo $a['budget']; ?></label>
    <input type="text" class="form-control" id="sc_budget" name="sc_budget" maxlength="<?php echo $a['budget_max'];?>">
  </div>
  <div class="form-group">
    <label for="sc_message"><?php echo $a['message']; ?></label>
    <textarea class="form-control" id="sc_message" name="sc_message" rows="<?php echo $a['message_rows']; ?>" cols="<?php echo $a['message_cols'];?>" maxlength="<?php echo $a['message_max'];?>">
    </textarea>
  </div>
  <?php wp_nonce_field( 'lead_form_custom_action', 'lead_form_nonce' ); ?>
  <input type="hidden" name="datetime" value="<?php echo $date_time; ?>">
  <input type="hidden" name="action" value="lead_form_custom_action">
  <div class="form-group">
    <div class="spinner-border" role="status" id="loading-lead-collector" style="display:none;">
    <span class="sr-only">Loading...</span>
   </div>
    <button type="submit" class="btn btn-primary" id="submit-lead-collector">Submit</button>
  </div>
</form>
<div class="alert alert-success" role="alert" id="success-lead-collector" style="display:none;">
    Submitted Successfully! 
</div>