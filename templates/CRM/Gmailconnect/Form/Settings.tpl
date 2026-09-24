<div class="crm-block crm-form-block">
  <p>{ts}Copy this URL into the CiviCRM Connect Gmail add-on. It is personal to you: anyone who has it can look up contacts and record emails in CiviCRM as you, so don't share it.{/ts}</p>
  <div class="crm-section">
    <div class="label"><label for="gmailconnect-url">{ts}Your Gmail Connect URL{/ts}</label></div>
    <div class="content">
      <input type="text" id="gmailconnect-url" class="crm-form-text huge" value="{$endpointUrl|escape}" readonly onclick="this.select();">
      <button type="button" class="crm-button" onclick="navigator.clipboard.writeText(document.getElementById('gmailconnect-url').value); CRM.status('{ts escape='js'}Copied{/ts}');">{icon icon="fa-clipboard"}{/icon} {ts}Copy{/ts}</button>
    </div>
    <div class="clear"></div>
  </div>
  <p class="description">{ts}Regenerating creates a new URL. The current one stops working immediately.{/ts}</p>
  <div class="crm-submit-buttons">{include file="CRM/common/formButtons.tpl" location="bottom"}</div>
</div>
