export default function(params) {
    var defaultParams = {
        'repoContainer': '[data-role="raas-repo-container"]',
        'repoElement': '[data-role="raas-repo-element"]',
        'repoElementChanges': {'data-role': 'raas-repo-element'},
        'repoAdd': '[data-role="raas-repo-add"]',
        'repoMove': '[data-role="raas-repo-move"]',
        'repoDelete': '[data-role="raas-repo-del"]',
        'repo': '[data-role="raas-repo"]',
        'onBeforeAdd': function() {},
        'onAfterAdd': function() { $(this).find('select:disabled, input:disabled, textarea:disabled').removeAttr('disabled'); },
        'onBeforeDelete': function() {},
        'onAfterDelete': function() {}
    }
    params = $.extend(defaultParams, params);
    var $repoBlock = $(this);
    
    var $repoContainer;
    if ($(this).attr('data-raas-repo-container')) {
        $repoContainer = $($(this).attr('data-raas-repo-container'));
    } else if ($repoBlock.find(params.repoContainer).length > 0) {
        $repoContainer = $repoBlock.find(params.repoContainer);
    } else {
        $repoContainer = $(params.repoContainer);
    }
    
    var $repo;
    if ($(this).attr('data-raas-repo')) {
        $repo = $($(this).attr('data-raas-repo'));
    } else if ($repoBlock.find(params.repo).length > 0) {
        $repo = $repoBlock.find(params.repo);
    } else {
        $repo = $(params.repo);
    }

    var checkRequired = function() {
        var $repoElement;
        if ($repoBlock.find(params.repoElement).length > 0) {
            $repoElement = $repoBlock.find(params.repoElement + ':has(*[data-required])');
        } else {
            $repoElement = $(params.repoElement + ':has(*[data-required])');
        }
        if ($repoElement.length > 1) {
            $repoElement.find(params.repoDelete).show();
        } else {
            $repoElement.find(params.repoDelete).hide();
        }

        if ($repoBlock.find(params.repoElement).length > 0) {
            $repoElement = $repoBlock.find(params.repoElement);
        } else {
            $repoElement = $(params.repoElement);
        }
        if ($repoElement.length > 1) {
            $repoElement.find(params.repoMove).show();
        } else {
            $repoElement.find(params.repoMove).hide();
        }
    };

    $repoBlock.on('click', params.repoAdd, function() {
        params.onBeforeAdd.call($repoElement);
        var $repoElement = $repo.clone(true);
        $repoElement.attr(params.repoElementChanges);
        $repoContainer.append($repoElement);
        $repoElement.trigger('RAAS_repo.add');
        params.onAfterAdd.call($repoElement);
        checkRequired();
        $repoElement.RAASInitInputs();
        return false;
    });
    
    $repoBlock.on('click', params.repoDelete, function() {
        var $repoElement;
        if ($(this).closest(params.repoElement).length > 0) {
            $repoElement = $(this).closest(params.repoElement);
        } else if ($(this).attr('data-raas-repo-element')) {
            $repoElement = $($(this).attr('data-raas-repo-element'));
        } else if ($repoBlock.find(params.repoElement).length > 0) {
            $repoElement = $repoBlock.find(params.repoElement);
        } else {
            $repoElement = $(params.repoElement);
        }
        params.onBeforeDelete.call($repoElement);
        $repoElement.trigger('RAAS_repo.delete');
        $repoElement.remove();
        params.onAfterDelete.call($repoElement);
        checkRequired();
        return false;
    });

    $repoContainer.sortable({ axis: 'y', 'handle': params.repoMove, containment: $(this) });


    checkRequired();
};