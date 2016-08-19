<?php /* Smarty version Smarty-3.1.19, created on 2016-08-19 17:15:46
         compiled from "C:\Personal\XAMPP\htdocs\prestashopV3\themes\default-bootstrap\category-count.tpl" */ ?>
<?php /*%%SmartyHeaderCode:147857b722a2e27a73-17783174%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae718ba0fefbcf68b5f89929eb701aab3f67c5bc' => 
    array (
      0 => 'C:\\Personal\\XAMPP\\htdocs\\prestashopV3\\themes\\default-bootstrap\\category-count.tpl',
      1 => 1466017274,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '147857b722a2e27a73-17783174',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category' => 0,
    'nb_products' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57b722a30cc3a2_73033688',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b722a30cc3a2_73033688')) {function content_57b722a30cc3a2_73033688($_smarty_tpl) {?>
<span class="heading-counter"><?php if ((isset($_smarty_tpl->tpl_vars['category']->value)&&$_smarty_tpl->tpl_vars['category']->value->id==1)||(isset($_smarty_tpl->tpl_vars['nb_products']->value)&&$_smarty_tpl->tpl_vars['nb_products']->value==0)) {?><?php echo smartyTranslate(array('s'=>'There are no products in this category.'),$_smarty_tpl);?>
<?php } else { ?><?php if (isset($_smarty_tpl->tpl_vars['nb_products']->value)&&$_smarty_tpl->tpl_vars['nb_products']->value==1) {?><?php echo smartyTranslate(array('s'=>'There is 1 product.'),$_smarty_tpl);?>
<?php } elseif (isset($_smarty_tpl->tpl_vars['nb_products']->value)) {?><?php echo smartyTranslate(array('s'=>'There are %d products.','sprintf'=>$_smarty_tpl->tpl_vars['nb_products']->value),$_smarty_tpl);?>
<?php }?><?php }?></span>
<?php }} ?>
