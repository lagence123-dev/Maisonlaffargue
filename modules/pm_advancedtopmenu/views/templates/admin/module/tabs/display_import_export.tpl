<form action="{$base_config_url|atm_nofilter}#config-7" id="formImport_{$module_name|escape:'htmlall':'UTF-8'}" name="formImport_{$module_name|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal" enctype="multipart/form-data">
    <h2>{l s='Import a menu' mod='pm_advancedtopmenu'}</h2>
    <fieldset>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Import from JSON' mod='pm_advancedtopmenu'}</label>
            <div class="col-lg-9">
                <input type="file" name="importedFile" style="padding-top: 7px" accept="application/json" />
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3"></label>
            <div class="importCheckboxContainer">
                <input required type="checkbox" name="confirmImportDestructive" id="confirmImportDestructive" />
                <label for="confirmImportDestructive">{l s='I understand this operation requires entities IDs to be strictly identical between the original shop and this one, and that it will irreversibly delete my current menu and/or configuration to apply the new one' mod='pm_advancedtopmenu'}</label>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="submitImport" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='   Import   ' mod='pm_advancedtopmenu'}
            </button>
        </div>
    </fieldset>
</form>

<form action="{$base_config_url|atm_nofilter}#config-7" id="formExport_{$module_name|escape:'htmlall':'UTF-8'}" name="formExport_{$module_name|escape:'htmlall':'UTF-8'}" method="post" class="form-horizontal" enctype="multipart/form-data">
    <h2>{l s='Export a menu' mod='pm_advancedtopmenu'}</h2>
    <fieldset>
        <div class="form-group">
            <label class="control-label col-lg-3">{l s='Export to JSON' mod='pm_advancedtopmenu'}</label>
            <div class="col-lg-9">
                <p>{l s='The export will generate a JSON file representing your menu and/or the global and mobile configuration of the module. Importing a menu from this export will require that the destination shop has the exact same IDs for all entities (categories, manufacturers, products, etc...) than the source one. If this is not the case, the imported menu will not work, and will generate errors on your shop.' mod='pm_advancedtopmenu'}</p>
                <p>{l s='The images of your menu will have to be copied manually through FTP (the folders are `column_icons`, `element_icons` and `menu_icons` in our module\'s folder).' mod='pm_advancedtopmenu'}</p>
            </div>
        <div class="form-group">
            <label class="control-label col-lg-3"></label>
            <div>
                <input type="checkbox" name="exportMenu" id="exportMenu" />
                <label for="exportMenu">{l s='Export menu?' mod='pm_advancedtopmenu'}</label>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-lg-3"></label>
            <div>
                <input type="checkbox" name="exportConfig" id="exportConfig" />
                <label for="exportConfig">{l s='Export configuration?' mod='pm_advancedtopmenu'}</label>
            </div>
        </div>
        <div class="form-group">
            <button type="submit" name="submitExport" class="btn btn-default pull-right">
                <i class="process-icon-save"></i>
                {l s='   Export   ' mod='pm_advancedtopmenu'}
            </button>
        </div>
    </fieldset>
</form>
