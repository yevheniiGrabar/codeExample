<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class AbilityEnum extends Enum
{
    // Customers
    public const CUSTOMER_INDEX = 'customer.index';
    public const CUSTOMER_CREATE = 'customer.create';
    public const CUSTOMER_UPDATE = 'customer.update';
    public const CUSTOMER_DELETE = 'customer.delete';
    public const CUSTOMER_SHOW = 'customer.show';
    public const CUSTOMER_EXPORT = 'customer.export';
    public const CUSTOMER_IMPORT = 'customer.import';

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
    public const PURCHASE_ORDER_EXPORT = 'purchase_order.export';
    public const PURCHASE_ORDER_IMPORT = 'purchase_order.import';

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
    public const SALE_ORDER_EXPORT = 'sale_order.export';

    // Picking
    public const PICKING_INDEX = 'picking.index';
    public const PICKING_UPDATE = 'picking.update';
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
    public const USER_INVITE = 'user.invite';

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
//    public const DASHBOARD_SALES_ACTIVITY = 'dashboard.sales_activity';
//    public const DASHBOARD_BEST_SELLING_PRODUCTS = 'dashboard.best_selling_products';
//    public const DASHBOARD_REVENUE = 'dashboard.revenue';
//    public const DASHBOARD_RESTOCKING = 'dashboard.restocking';
//    public const DASHBOARD_FEED = 'dashboard.feed';
}
