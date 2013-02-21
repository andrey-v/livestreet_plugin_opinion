<li {if $sAction=='profile' && $aParams[0]=='votinginfo'}class="active"{/if}>
    <a href="{$oUserProfile->getUserWebPath()}votinginfo/">{$aLang.plugin.opinion.voting_info}
    </a>
    {if $iCountNewOpinion}<span title="New opinion">+{$iCountNewOpinion}</span>{/if}
</li>
