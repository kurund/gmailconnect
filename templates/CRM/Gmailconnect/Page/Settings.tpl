<div class="crm-block crm-content-block">
  {if $contact}
    {if $contact.is_deleted}
      <div class="messages status no-popup">
        {icon icon="fa-exclamation-triangle"}{/icon}
        {ts}The Gmail Connect contact is in the trash. API requests will fail until it is restored.{/ts}
      </div>
    {/if}
    <table class="crm-info-panel">
      <tr>
        <td class="label">{ts}Contact{/ts}</td>
        <td><a href="{$contact.url|escape}">{$contact.display_name|escape}</a> ({ts}ID{/ts} {$contact.id})</td>
      </tr>
      <tr>
        <td class="label">{ts}WordPress user{/ts}</td>
        <td>{if $contact.user_login}{$contact.user_login|escape} &lt;{$contact.user_email|escape}&gt;{else}<em>{ts}No linked WordPress user found{/ts}</em>{/if}</td>
      </tr>
      <tr>
        <td class="label">{ts}API key{/ts}</td>
        <td>{if $contact.api_key}<code>{$contact.api_key|escape}</code>{else}<em>{ts}Not set{/ts}</em>{/if}</td>
      </tr>
      <tr>
        <td class="label">{ts}Endpoint base URL{/ts}</td>
        <td><code>{$endpointUrl|escape}</code></td>
      </tr>
    </table>
  {else}
    <div class="messages status no-popup">
      {icon icon="fa-info-circle"}{/icon}
      {ts}The Gmail Connect contact could not be found. Uninstall and reinstall the extension to set it up again.{/ts}
    </div>
  {/if}
</div>
