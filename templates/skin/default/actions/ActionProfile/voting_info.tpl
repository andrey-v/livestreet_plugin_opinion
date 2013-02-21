{include file='header.tpl' menu='people'}
{include file='actions/ActionProfile/profile_top.tpl'}
{* Здесь начало содержимого страницы *}

<div class="votes votes-list">
{if $aVotes}
    <div class="talk-search" id="block_talk_search">
        <header>
            <button type="submit"
                    onclick="if (confirm('{$aLang.plugin.opinion.delete_alert}')){ ls.vote.removeVotes() };"
                    class="button">{$aLang.plugin.opinion.delete_selected}
            </button>
        </header>
    </div>
    <form action="{$oUserProfile->getUserWebPath()}votinginfo/" method="post" id="form_votes_list">
        <table class="table table-votes">
            <thead>
            <tr>
                <th class="cell-checkbox">
                    <input type="checkbox" name="" class="input-checkbox"
                           onclick="ls.tools.checkAll('form_votes_checkbox', this, true);">
                </th>
                <th class="cell-vote-types">{$aLang.plugin.opinion.cell_header_type}</th>
                <th class="cell-vote-count">{$aLang.plugin.opinion.cell_header_votes}</th>
                <th class="cell-vote-title">{$aLang.plugin.opinion.cell_header_messages}</th>
            </tr>
            </thead>
            <tbody>
                {foreach from=$aVotes item=oVote}
                <tr class="vote-line {if $oVote['vote_value']<0}bad-vote{else}good-vote{/if}">
                    <td class="cell-checkbox"><input type="checkbox" name="vote_select[{$oVote['id']}]"
                                                     class="form_votes_checkbox input-checkbox"></td>
                    <td class="cell-vote-types">
                        {if $oVote['target_type']=='comment'}{$aLang.plugin.opinion.comment}{/if}
                        {if $oVote['target_type']=='blog'}{$aLang.plugin.opinion.blog}{/if}
                        {if $oVote['target_type']=='user'}{$aLang.plugin.opinion.user}{/if}
                        {if $oVote['target_type']=='topic'}{$aLang.plugin.opinion.topic}{/if}
                        {if $aNewOpinion and $oVote['id']|in_array:$aNewOpinion}
                            <sup style="color: green;">new</sup>
                        {/if}
                    </td>
                    <td class="cell-vote-count {if $oVote['vote_value']<0}bad-vote{else}good-vote{/if}">{if $oVote['vote_value']<0}-{else}+{/if}</td>
                    <td class="cell-vote-title">
                        <a href="{$oVote['url']}">{$oVote['comment']}</a>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        <input type="hidden" id="form_votes_list_submit_del" name="form_votes_list_submit_del" value="0">
    </form>
    {else}
    {$aLang.plugin.opinion.empty_page}
{/if}
</div>

{include file='paging.tpl' aPaging=$aPaging}

{* А здесь ее окончание *}
{include file='footer.tpl'}
