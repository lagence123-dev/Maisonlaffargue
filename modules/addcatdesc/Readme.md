Additional description of category for Prestashop 1.7.x

Installation:
1 . To copy the folder with the module in the modules folder.
2 . To install through admin panel of your shop (Front Office tab).
3 . Only for prestashop less then 1.7.7 - To add in that place where you need to output the additional description in the templates\catalog\listing\product-list.tpl file (usually after the 66th line (different for different prestashop versions)) the next lines:

<!-- Additional category description -->
{hook h='displayAddCatDesc' mod='addcatdesc'}

The module is ready to work. And don't forget to clear cache.

#1.7.1 - partial view added, first page only view added.
#1.7.3 - bugs with positions fixed.
#1.7.4 - support of new forms in category page in back office of prestashop 1.7.6.x added.
#1.7.5 - support of new forms in category page in back office of prestashop 1.7.6.x added. Bugs fixed.
#1.7.6 - Minor bugs with languages fixed.
#1.7.7 - Major update, controller structure changed, minor bugs fixed. Full support for first page view added.
#1.7.8 - Support of utf8mb4 charset added for prestashop 1.7.7
#1.7.9 - Support of Home category - 2 added for prestashop 1.7.x
#1.8.0 - Support of displayFooterCategory hook added for prestashop 1.7.7
#1.8.2 - Bug with hookCategoryUpdate fixed, Search field in list of categories added for prestashop 1.7.x 
#1.8.3 - Parameters: height of partial view and button position of partial view added for prestashop 1.7.x
#1.8.5 - Bugs with multishops fixed for prestashop 1.7.x, additional selection for language added for multilanguage for prestashop 1.7.x
#1.8.6 - Support of new forms in module page of prestashop 1.7.8.0 added.
#1.8.7 - Minor changes of new forms in module page of #1.8.6 version.
#1.8.8 - Minor changes in the settings form of the module page for prestashop >= 1.8.0.
#1.8.9 - Major changes for prestashop >= 1.7.6.0. FAQ added. Template functionalities added.
#1.9.0 - Minor changes in css for Front office.
#1.9.1 - Support of shortcodes for products added.
#1.9.2 - Information tab for shortcodes for prestashop < 8.0 added.
#1.9.3 - Support of shortcodes for category added.
#1.9.4 - Minor bug with shortcodes in partial view fixed.
#1.9.5 - New translation added.
#1.9.6 - First page only view added for FAQ.
#1.9.7 - Spanish language added.
#1.9.8 - German, French, Poland, Italian, Ukranian languages added.