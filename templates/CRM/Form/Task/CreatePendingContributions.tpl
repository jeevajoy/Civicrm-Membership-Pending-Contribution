{*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<div class="crm-block crm-form-block crm-export-form-block">

<h2>{ts}Renew Memberships{/ts}</h2>
<div class="help">
<p>{ts}Use this Form is to renew membership for the selected records and to create corresponding contributions.<br />To move start date and end date please check the tick box below. if the tick box is not ticked, only a contribution record will be created and the membership dates will not be moved.<br />* Note that this action is irreversible.{/ts}</p>
</div>
<h3>{ts}Summary{/ts}</h3>
<table class="report" style="width: 100%">
       
	<tr>
           {if $totalAddedContributions}
           <td>
           <div class="crm-accordion-wrapper crm-accordion_title-accordion crm-accordion-closed">
           <div class="crm-accordion-header">
           <div class="icon crm-accordion-pointer"></div>
            Number of selected memberships: {$contributionsCount}
           </div><!-- /.crm-accordion-header -->
           <div class="crm-accordion-body">
           <table class="selector">
           <thead >
	      <tr>
                 <th>{ts}Name{/ts}</th>
                 <th>{ts}Membership Type{/ts}</th>
                 <th>{ts}Start Date{/ts}</th>
                 <th>{ts}End Date{/ts}</th>                  
    	         <th>{ts}Amount{/ts}</th>
    	         <th>{ts}Contribution Type{/ts}</th>
    	        <!-- <th>{ts}Source{/ts}</th> --->
    	         <th>{ts}Receive Date{/ts}</th>
	      </tr>
            </thead>
             {foreach from=$totalAddedContributions item=row}
             <tr>
                <td><a href="{crmURL p='civicrm/contact/view' q="reset=1&cid=`$row.contact_id`"}">{$row.display_name}</a></td>
                <td>{$row.membership_type}</td>
                <td>{$row.membership_start_date}</td>
                <td>{$row.membership_end_date}</td>
                <td>{$row.total_amount}</td>
                <td>{$row.contribution_type}</td>
                <!--<td>{$row.source}</td> -->
                <td>{$row.receive_date}</td>
              </tr>
             {/foreach}
           </table>
	   </div><!-- /.crm-accordion-body -->
           </div><!-- /.crm-accordion-wrapper -->
           </td>
           {else}
             <td>
                 <div class="crm-accordion-header">
                 Number of selected contributions: {$totalAddedContributions}
                 </div>
             </td>
           {/if}
        </tr>
</table>
<table> 
<tr> <td>{$form.is_renew.label}    {$form.is_renew.html} </td><tr>
<tr> <td><span  style=?font-size:10?>(Please tick this box if you want to move the membership start date and end date )</span> </td></tr>
</table>

{$form.buttons.html}

</div>
{literal}
<script type="text/javascript">
cj(function() {
   cj().crmaccordions(); 
});
</script>
{/literal}