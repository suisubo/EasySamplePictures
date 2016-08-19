<?php /* Smarty version Smarty-3.1.19, created on 2016-08-19 17:17:55
         compiled from "C:\Personal\XAMPP\htdocs\prestashopV3\admin\themes\default\template\helpers\tree\tree_node_item.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1905057b723230d5b02-76283129%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9254928c51384d0611f62cf1825236886100e4ad' => 
    array (
      0 => 'C:\\Personal\\XAMPP\\htdocs\\prestashopV3\\admin\\themes\\default\\template\\helpers\\tree\\tree_node_item.tpl',
      1 => 1466017274,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1905057b723230d5b02-76283129',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'node' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_57b72323112ec0_48988800',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_57b72323112ec0_48988800')) {function content_57b72323112ec0_48988800($_smarty_tpl) {?>

<li class="tree-item">
	<span class="tree-item-name">
		<i class="tree-dot"></i>
		<label class="tree-toggler"><?php echo $_smarty_tpl->tpl_vars['node']->value['name'];?>
</label>
	</span>
</li><?php }} ?>
