<?php

namespace App\Enums\Permissions;

use App\Enums\AbstractEnum;

final class Access extends AbstractEnum
{
    // Customers
    public const CUSTOMER_INDEX = 'customer.index';
    public const CUSTOMER_CREATE = 'customer.create';
    public const CUSTOMER_UPDATE = 'customer.update';
    public const CUSTOMER_DELETE = 'customer.delete';
    public const CUSTOMER_SHOW = 'customer.show';
    public const CUSTOMER_EXPORT = 'customer.export';

    // Suppliers
    public const SUPPLIER_INDEX = 'supplier.index';
    public const SUPPLIER_CREATE = 'supplier.create';
    public const SUPPLIER_UPDATE = 'supplier.update';
    public const SUPPLIER_DELETE = 'supplier.delete';
    public const SUPPLIER_SHOW = 'supplier.show';
    public const SUPPLIER_EXPORT = 'supplier.export';

    // Products
    public const PRODUCT_INDEX = 'product.index';
    public const PRODUCT_CREATE = 'product.create';
    public const PRODUCT_UPDATE = 'product.update';
    public const PRODUCT_DELETE = 'product.delete';
    public const PRODUCT_SHOW = 'product.show';
    public const PRODUCT_EXPORT = 'product.export';
    public const PRODUCT_IMPORT = 'product.import';

    // *Adjustments*
    public const ADJUSTMENT_INDEX = 'adjustment.index';
    public const ADJUSTMENT_CREATE = 'adjustment.create';
    public const ADJUSTMENT_SHOW = 'adjustment.show';

    // *Transfers*
    public const TRANSFER_INDEX = 'transfer.index';
    public const TRANSFER_CREATE = 'transfer.create';
    public const TRANSFER_SHOW = 'transfer.show';
    public const TRANSFER_EXPORT = 'transfer.export';

    // Location
    public const LOCATION_INDEX = 'location.index';
    public const LOCATION_CREATE = 'location.create';
    public const LOCATION_UPDATE = 'location.update';
    public const LOCATION_DELETE = 'location.delete';
    public const LOCATION_SHOW = 'location.show';

    // Purchase order
    public const PURCHASE_ORDER_INDEX = 'purchase_order.index';
    public const PURCHASE_ORDER_CREATE = 'purchase_order.create';
    public const PURCHASE_ORDER_UPDATE = 'purchase_order.update';
    public const PURCHASE_ORDER_DELETE = 'purchase_order.delete';
    public const PURCHASE_ORDER_SHOW = 'purchase_order.show';

    // Receive
    public const RECEIVE_INDEX = 'receive.index';
    public const RECEIVE_CREATE = 'receive.create';
    public const RECEIVE_DELETE = 'receive.delete';
    public const RECEIVE_SHOW = 'receive.show';

    // Sale Order
    public const SALE_ORDER_INDEX = 'sale_order.index';
    public const SALE_ORDER_CREATE = 'sale_order.create';
    public const SALE_ORDER_UPDATE = 'sale_order.update';
    public const SALE_ORDER_DELETE = 'sale_order.delete';
    public const SALE_ORDER_SHOW = 'sale_order.show';

    // Picking
    public const PICKING_INDEX = 'picking.index';
    public const PICKING_UPDATE = 'picking.create';
    public const PICKING_SHOW = 'picking.show';

    // Company
    public const COMPANY_INDEX = 'company.index';
    public const COMPANY_CREATE = 'company.create';
    public const COMPANY_UPDATE = 'company.update';
    public const COMPANY_SHOW = 'company.show';

    //Employees
    public const EMPLOYEE_INDEX = 'employee.index';
    public const EMPLOYEE_CREATE = 'employee.create';
    public const EMPLOYEE_UPDATE = 'employee.update';
    public const EMPLOYEE_DELETE = 'employee.delete';
    public const EMPLOYEE_SHOW = 'employee.show';

    //Tax
    public const TAX_INDEX = 'tax.index';
    public const TAX_CREATE = 'tax.create';
    public const TAX_UPDATE = 'tax.update';
    public const TAX_DELETE = 'tax.delete';
    public const TAX_SHOW = 'tax.show';

    //LANGUAGE
    public const LANGUAGE_INDEX = 'language.index';
    public const LANGUAGE_CREATE = 'language.create';
    public const LANGUAGE_DELETE = 'language.delete';

    //CURRENCY
    public const CURRENCY_INDEX = 'currency.index';
    public const CURRENCY_CREATE = 'currency.create';
    public const CURRENCY_DELETE = 'currency.delete';

    //Payment term
    public const PAYMENT_TERM_INDEX = 'payment_term.index';
    public const PAYMENT_TERM_CREATE = 'payment_term.create';
    public const PAYMENT_TERM_UPDATE = 'payment_term.update';
    public const PAYMENT_TERM_DELETE = 'payment_term.delete';
    public const PAYMENT_TERM_SHOW = 'payment_term.show';

    //Delivery term
    public const DELIVERY_TERM_INDEX = 'delivery_term.index';
    public const DELIVERY_TERM_CREATE = 'delivery_term.create';
    public const DELIVERY_TERM_UPDATE = 'delivery_term.update';
    public const DELIVERY_TERM_DELETE = 'delivery_term.delete';
    public const DELIVERY_TERM_SHOW = 'delivery_term.show';

    //PROFILE
    public const PROFILE_UPDATE = 'profile.update';

    //User
    public const USER_INDEX = 'user.index';
    public const USER_CREATE = 'user.create';
    public const USER_UPDATE = 'user.update';
    public const USER_DELETE = 'user.delete';
    public const USER_SHOW = 'user.show';

    //Roles
    public const ROLE_INDEX = 'role.index';
    public const ROLE_CREATE = 'role.create';
    public const ROLE_UPDATE = 'role.update';
    public const ROLE_DELETE = 'role.delete';
    public const ROLE_SHOW = 'role.show';

    //Package
    public const PACKAGE_INDEX = 'package.index';
    public const PACKAGE_CREATE = 'package.create';
    public const PACKAGE_UPDATE = 'package.update';
    public const PACKAGE_DELETE = 'package.delete';
    public const PACKAGE_SHOW = 'package.show';

    //Category
    public const CATEGORY_INDEX = 'category.index';
    public const CATEGORY_CREATE = 'category.create';
    public const CATEGORY_UPDATE = 'category.update';
    public const CATEGORY_DELETE = 'category.delete';
    public const CATEGORY_SHOW = 'category.show';

    //DASHBOARD
    public const DASHBOARD_SALES_ACTIVITY = 'dashboard.sales_activity';
    public const DASHBOARD_BEST_SELLING_PRODUCTS = 'dashboard.best_selling_products';
    public const DASHBOARD_REVENUE = 'dashboard.revenue';
    public const DASHBOARD_RESTOCKING = 'dashboard.restocking';
    public const DASHBOARD_FEED = 'dashboard.feed';


//    // Purchases
//    public const VIEW_PURCHASES = 'view-purchases';
//    public const CREATE_PURCHASES = 'create-purchases';
//    public const UPDATE_PURCHASES = 'update-purchases';
//    public const DELETE_PURCHASES = 'delete-purchases';
//    public const SHOW_PURCHASES = 'show-purchases';
//
//    // *INVENTORY* //
//
//    // *Stock*
//    public const VIEW_STOCKS = 'view-stocks';
//    public const EXPORT_STOCKS = 'export-stocks';
//
//
//
//
//
//
//
//    // Company
//    public const VIEW_COMPANIES = 'view-companies';
//    public const CREATE_COMPANIES = 'create-companies';
//    public const UPDATE_COMPANIES = 'update-companies';
//    public const DELETE_COMPANIES = 'delete-companies';
//    public const SHOW_COMPANIES = 'show-companies';
//
//    // Industry
//    public const VIEW_INDUSTRIES = 'view-industries';
//    public const CREATE_INDUSTRIES = 'create-industries';
//    public const UPDATE_INDUSTRIES = 'update-industries';
//    public const DELETE_INDUSTRIES = 'delete-industries';
//    public const SHOW_INDUSTRIES = 'show-industries';
//
//    // Currency
//    public const VIEW_CURRENCIES = 'view-currencies';
//    public const CREATE_CURRENCIES = 'create-currencies';
//    public const UPDATE_CURRENCIES = 'update-currencies';
//    public const DELETE_CURRENCIES = 'delete-currencies';
//    public const SHOW_CURRENCIES = 'show-currencies';
//
//    // Language
//    public const VIEW_LANGUAGES = 'view-languages';
//    public const CREATE_LANGUAGES = 'create-languages';
//    public const UPDATE_LANGUAGES = 'update-languages';
//    public const DELETE_LANGUAGES = 'delete-languages';
//    public const SHOW_LANGUAGES = 'show-languages';
//
//    // Delivery Address
//    public const VIEW_DELIVERY_ADDRESSES = 'view-delivery-addresses';
//    public const CREATE_DELIVERY_ADDRESS = 'create-delivery-addresses';
//    public const UPDATE_DELIVERY_ADDRESS = 'update-delivery-addresses';
//    public const DELETE_DELIVERY_ADDRESS = 'delete-delivery-addresses';
//    public const SHOW_DELIVERY_ADDRESS = 'show-delivery-addresses';
//
//    // Billing Address
//    public const VIEW_BILLING_ADDRESSES = 'view-billing-addresses';
//    public const CREATE_BILLING_ADDRESS = 'create-billing-addresses';
//    public const UPDATE_BILLING_ADDRESS = 'update-billing-addresses';
//    public const DELETE_BILLING_ADDRESS = 'delete-billing-addresses';
//    public const SHOW_BILLING_ADDRESS = 'show-billing-addresses';
//
//    // Shipping Address
//    public const VIEW_SHIPPING_ADDRESSES = 'view-shipping-addresses';
//    public const CREATE_SHIPPING_ADDRESS = 'create-shipping-addresses';
//    public const UPDATE_SHIPPING_ADDRESS = 'update-shipping-addresses';
//    public const DELETE_SHIPPING_ADDRESS = 'delete-shipping-addresses';
//    public const SHOW_SHIPPING_ADDRESS = 'show-shipping-addresses';
//
//    // Sub Location
//    public const VIEW_SUB_LOCATIONS = 'view-sub-locations';
//    public const CREATE_SUB_LOCATIONS = 'create-sub-locations';
//    public const UPDATE_SUB_LOCATIONS = 'update-sub-locations';
//    public const DELETE_SUB_LOCATIONS = 'delete-sub-locations';
//    public const SHOW_SUB_LOCATIONS = 'show-sub-locations';
//
//    // Sale Return
//    public const VIEW_SALE_RETURNS = 'view-sale-returns';
//    public const CREATE_SALE_RETURNS = 'create-sale-returns';
//    public const UPDATE_SALE_RETURNS = 'update-sale-returns';
//    public const DELETE_SALE_RETURNS = 'delete-sale-returns';
//    public const SHOW_SALE_RETURNS = 'show-sale-returns';
//
//
//
//    // Shipment
//    public const VIEW_SHIPMENTS = 'view-shipments';
//    public const CREATE_SHIPMENTS = 'create-shipments';
//    public const UPDATE_SHIPMENTS = 'update-shipments';
//    public const DELETE_SHIPMENTS = 'delete-shipments';
//    public const SHOW_SHIPMENTS = 'show-shipments';
//
//    // Picking
//    public const VIEW_PICKINGS = 'view-pickings';
//    public const SHOW_PICKINGS = 'show-pickings';
//
//    // Employee
//    public const VIEW_EMPLOYEES = 'view-employees';
//    public const CREATE_EMPLOYEES = 'create-employees';
//    public const UPDATE_EMPLOYEES = 'update-employees';
//    public const DELETE_EMPLOYEES = 'delete-employees';
//    public const SHOW_EMPLOYEES = 'show-employees';
//
//    // Calendar
//    public const VIEW_CALENDARS = 'view-calendars';
//    public const CREATE_CALENDARS = 'create-calendars';
//    public const UPDATE_CALENDARS = 'update-calendars';
//    public const DELETE_CALENDARS = 'delete-calendars';
//    public const SHOW_CALENDARS = 'show-categories';
//
//    // Category
//    public const VIEW_CATEGORIES = 'view-categories';
//    public const CREATE_CATEGORIES = 'create-categories';
//    public const UPDATE_CATEGORIES = 'update-categories';
//    public const DELETE_CATEGORIES = 'delete-categories';
//    public const SHOW_CATEGORIES = 'show-categories';
//
////----------------------------------------------------------------------------------------------------------------------
//
//    // Permissions settings
//    public const VIEW_PERMISSION_SETTINGS = 'view-permission';
//    public const UPDATE_PERMISSION_SETTINGS = 'update-permission';
//    public const CREATE_PERMISSION_SETTINGS = 'create-permission';
//    public const DELETE_PERMISSION_SETTINGS = 'delete-permission';
//
//    // company Users
//    public const VIEW_COMPANY_USERS_LIST = 'view-user-list';
//    public const VIEW_COMPANY_USER_CONFIG = 'view-user-config';
//    public const UPDATE_CURRENT_COMPANY_USER = 'update-current-user';
//    public const EXPORTS_COMPANY_USERS = 'export-users';
//    public const CREATE_COMPANY_USERS = 'create-users';
//    public const VIEW_CURRENT_COMPANY_USER = 'view-current-user';
//    public const DELETE_COMPANY_USERS = 'delete-users';
//    public const UPDATE_COMPANY_USERS = 'update-users';
//
//    //    //components
////    public const VIEW_COMPONENTS_LIST = 'View list of components';
////    public const ADD_COMPONENT = 'Add new component';
////    public const VIEW_CURRENT_COMPONENT = 'View current component';
////    public const UPDATE_COMPONENT = 'Update component';
////    public const DELETE_COMPONENT = 'Delete component';
//
//    // Users
//    public const VIEW_LIST_USERS = 'view-user-list';
//    public const VIEW_USER_CONFIG = 'view-user-config';
//    public const UPDATE_CURRENT_USER = 'update-current-user';
//    public const EXPORTS_USERS = 'export-users';
//    public const CREATE_USERS = 'create-users';
//    public const VIEW_CURRENT_USER = 'view-current-user';
//    public const DELETE_USERS = 'delete-users';
//    public const UPDATE_USERS = 'update-users';
//
//    //    //inventory
////    public const VIEW_LIST_INVENTORY = 'View list of inventories';
////    public const CREATE_INVENTORY = 'Create Inventory';
////    public const VIEW_CURRENT_INVENTORY = 'View current inventory';
////    public const UPDATE_INVENTORY = 'Update Inventory';
////    public const DELETE_INVENTORY = 'Delete Inventory';
//
//    //productionOrder
//    public const VIEW_PRODUCTION_ORDERS = 'view-production-order-list';
//    public const CREATE_PRODUCTION_ORDER = 'create-production-order';
//    public const VIEW_CURRENT_PRODUCTION_ORDER = 'view-current-production-order';
//    public const UPDATE_PRODUCTION_ORDER = 'update-production-order';
//    public const DELETE_PRODUCTION_ORDER = 'delete-production-order';
//
//    // Cabinet
//    public const VIEW_CABINET = 'view-cabinet';
//    public const UPDATE_CABINET = 'update-cabinet';
//
//    //supplier delivery
//    public const VIEW_LIST_SUPPLIER_DELIVERY = 'view-supplier-deliveries-list';
//    public const CREATE_SUPPLIER_DELIVERY = 'create-supplier-delivery';
//    public const VIEW_CURRENT_SUPPLIER_DELIVERY = 'view-current-supplier-delivery';
//    public const UPDATE_SUPPLIER_DELIVERY = 'update-supplier-delivery';
//    public const DELETE_SUPPLIER_DELIVERY = 'delete-supplier-delivery';
//
//    public const VIEW_ANALYTICS = 'view-analytics';
//
//    //unit
//    public const VIEW_LIST_UNIT = 'view-unit-list';
//    public const VIEW_CURRENT_UNIT = 'view-current-unit';
//    public const CREATE_UNIT = 'create-unit';
//    public const UPDATE_UNIT = 'update-unit';
//    public const DELETE_UNIT = 'delete-unit';
//
//    //Stock Movement
//    public const VIEW_LIST_OF_STOCK_MOVEMENTS = 'view-stock-movements-list';

}
