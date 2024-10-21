<?php

return [

    [
        'name' => __('Reports'),
        'description' => __('Reports'),
        'permissions' => [
            'client-credits'      =>['system.report.credit'],
            'upload-credits-sheet'      =>['system.report.upload-credit'],
            'total-dues'      =>['system.report.total-dues'],
            'credit match report'      =>['system.report.match'],
        ]
    ],

    [
        'name' => __('Push Notifications'),
        'description' => __('Push Notifications'),
        'permissions' => [
            'send-push-notifications'      =>['system.push-notifications.create','system.push-notifications.store'],
        ]
    ],


    [
        'name' => __('Banks'),
        'description' => __('Banks Permissions'),
        'permissions' => [
            'view-all-banks'  =>['system.bank.index'],
            'create-bank'      =>['system.bank.create','system.bank.store'],
        ]
    ],

    [
        'name' => __('Banks'),
        'description' => __('Banks Permissions'),
        'permissions' => [
            'view-all-banks'  =>['system.bank.index'],
            'create-bank'      =>['system.bank.create','system.bank.store'],
        ]
    ],

    [
        'name' => __('Banks Branches'),
        'description' => __('Banks Branches Permissions'),
        'permissions' => [
            'view-all-branches'  =>['system.bank-branch.index'],
            'create-branch'      =>['system.bank-branch.create','system.bank-branch.store'],
        ]
    ],

    [
        'name' => __('Maintenance'),
        'description' => __('Maintenance Permissions'),
        'permissions' => [
            'view-all-maintenance'  =>['system.maintenance.index'],
            'show-maintenance'        =>['system.maintenance.show'],
            //'delete-transaction'      =>['system.transaction.destroy','system.transaction.show'],
            //'create-transaction'      =>['system.transaction.create','system.transaction.store'],
            // 'update-transaction'      =>['system.transaction.edit','system.transaction.update']
        ]
    ],

    [
        'name' => __('Maintenance Categories'),
        'description' => __('Maintenance Categories Permissions'),
        'permissions' => [
            'view-all-maintenance-category'  =>['system.maintenance-category.index'],
            //'show-maintenance-category'        =>['system.maintenance-category.show'],
            //'delete-maintenance-category'      =>['system.maintenance-category.destroy','system.maintenance-category.show'],
            'create-maintenance-category'      =>['system.maintenance-category.create','system.maintenance-category.store'],
            'update-maintenance-category'      =>['system.maintenance-category.edit','system.maintenance-category.update']
        ]
    ],

    [
        'name' => __('Special Properties'),
        'description' => __('Special Properties Permissions'),
        'permissions' => [
            'view-all-special-properties'  =>['system.special-property.index'],
            'show-special-property'        =>['system.special-property.show'],
            'delete-special-property'      =>['system.special-property.destroy','system.special-property.show'],
            'create-special-property'      =>['system.special-property.create','system.special-property.store'],
            'update-special-property'      =>['system.special-property.edit','system.special-property.update']
        ]
    ],

    [
        'name' => __('Client Transactions'),
        'description' => __('Client Transactions Permissions'),
        'permissions' => [
            'view-all-client-transactions'  =>['system.client-transaction.index'],
            'show-client-transaction'        =>['system.client-transaction.show'],
            'delete-client-transaction'      =>['system.client-transaction.destroy','system.client-transaction.show'],
            'create-client-transaction'      =>['system.client-transaction.create','system.client-transaction.store'],
            'update-client-transaction'      =>['system.client-transaction.edit','system.client-transaction.update']
        ]
    ],

    [
        'name' => __('Transactions'),
        'description' => __('Transactions Permissions'),
        'permissions' => [
            'view-all-transactions'  =>['system.transaction.index'],
            'show-transaction'        =>['system.transaction.show'],
            //'delete-transaction'      =>['system.transaction.destroy','system.transaction.show'],
            //'create-transaction'      =>['system.transaction.create','system.transaction.store'],
           // 'update-transaction'      =>['system.transaction.edit','system.transaction.update']
        ]
    ],

    [
        'name' => __('Invoices'),
        'description' => __('Invoices Permissions'),
        'permissions' => [
            'view-all-invoices'   =>['system.invoice.index'],
            'show-invoice'        =>['system.invoice.show'],
            'delete-invoice'      =>['system.invoice.destroy','system.invoice.show'],
            'create-invoice'      =>['system.invoice.create','system.invoice.store'],
            'update-invoice'      =>['system.invoice.edit','system.invoice.update']
        ]
    ],

    [
        'name' => __('Facility Companies'),
        'description' => __('Facility Companies Permissions'),
        'permissions' => [
            'view-all-facility-companies'  =>['system.facility-companies.index'],
            'show-facility-company'        =>['system.facility-companies.show'],
            'delete-facility-company'      =>['system.facility-companies.destroy','system.facility-companies.show'],
            'create-facility-company'      =>['system.facility-companies.create','system.facility-companies.store'],
            'update-facility-company'      =>['system.facility-companies.edit','system.facility-companies.update']
        ]
    ],

    [
        'name' => __('Property Dues'),
        'description' => __('Property Dues Permissions'),
        'permissions' => [
            'view-all-property-dues'    =>['system.property-dues.index'],
            'show-property-dues-data'   =>['system.property-dues.show'],
            'delete-property-dues'      =>['system.property-dues.destroy','system.property-dues.show'],
            'create-property-dues'      =>['system.property-dues.create','system.property-dues.store'],
            'update-property-dues'      =>['system.property-dues.edit','system.property-dues.update']
        ]
    ],

    [
        'name' => __('Contracts'),
        'description' => __('Contracts Permissions'),
        'permissions' => [
            'view-all-contracts'    =>['system.contract.index'],
            'show-contract-data'    =>['system.contract.show'],
            'delete-contract'      =>['system.contract.destroy','system.contract.show'],
            'create-contract'      =>['system.contract.create','system.contract.store'],
            'update-contract'      =>['system.contract.edit','system.contract.update']
        ]
    ],

    [
        'name' => __('Contract Templates'),
        'description' => __('Contract Templates Permissions'),
        'permissions' => [
            'view-all-contract-templates'    =>['system.contract-template.index'],
            'show-contract-template-data'    =>['system.contract-template.show'],
            'delete-contract-template'      =>['system.contract-template.destroy','system.contract-template.show'],
            'create-contract-template'      =>['system.contract-template.create','system.contract-template.store'],
            'update-contract-template'      =>['system.contract-template.edit','system.contract-template.update']
        ]
    ],

    [
        'name' => __('Dues and Deductions'),
        'description' => __('Dues and Deductions Permissions'),
        'permissions' => [
            'view-all-dues'    =>['system.dues.index'],
            'show-dues-data'    =>['system.dues.show'],
            'delete-dues'      =>['system.dues.destroy','system.dues.show'],
            'create-dues'      =>['system.dues.create','system.dues.store'],
            'update-dues'      =>['system.dues.edit','system.dues.update']
        ]
    ],

    [
        'name' => __('Payment Methods'),
        'description' => __('Payment Methods Permissions'),
        'permissions' => [
            'view-all-payment-methods'    =>['system.payment-methods.index'],
            'show-payment-method-data'    =>['system.payment-methods.show'],
            'delete-payment-method'      =>['system.payment-methods.destroy','system.payment-methods.show'],
            'create-payment-method'      =>['system.payment-methods.create','system.payment-methods.store'],
            'update-payment-method'      =>['system.payment-methods.edit','system.payment-methods.update']
        ]
    ],

    [
        'name' => __('Incomes'),
        'description' => __('Incomes Permissions'),
        'permissions' => [
            'view-all-incomes'    =>['system.income.index'],
            'show-income-data'    =>['system.income.show'],
            'delete-income'      =>['system.income.destroy','system.income.show'],
            'create-income'      =>['system.income.create','system.income.store'],
            'update-income'      =>['system.income.edit','system.income.update']
        ]
    ],


    [
        'name' => __('Outcomes'),
        'description' => __('Outcomes Permissions'),
        'permissions' => [
            'view-all-outcomes'    =>['system.outcome.index'],
            'show-outcome-data'    =>['system.outcome.show'],
            'delete-outcome'      =>['system.outcome.destroy','system.outcome.show'],
            'create-outcome'      =>['system.outcome.create','system.outcome.store'],
            'update-outcome'      =>['system.outcome.edit','system.outcome.update']
        ]
    ],


    [
        'name' => __('Locker'),
        'description' => __('Locker Permissions'),
        'permissions' => [
            'view-all-lockers'    =>['system.locker.index'],
            //'view-one-locker'    =>['system.locker.show'],
            'delete-locker'      =>['system.locker.destroy','system.locker.show'],
            'create-locker'      =>['system.locker.create','system.locker.store'],
            'update-locker'      =>['system.locker.edit','system.locker.update']
        ]
    ],


    [
        'name' => __('Outcome Reasons'),
        'description' => __('Outcome Reasons Permissions'),
        'permissions' => [
            'view-all-outcome-reasons'    =>['system.outcome-reasons.index'],
            //'view-one-outcome-reasons'    =>['system.outcome-reasons.show'],
            'delete-outcome-reasons'      =>['system.outcome-reasons.destroy','system.outcome-reasons.show'],
            'create-outcome-reasons'      =>['system.outcome-reasons.create','system.outcome-reasons.store'],
            'update-outcome-reasons'      =>['system.outcome-reasons.edit','system.outcome-reasons.update']
        ]
    ],


    [
        'name' => __('Income Reasons'),
        'description' => __('Income Reasons Permissions'),
        'permissions' => [
            'view-all-income-reasons'    =>['system.income-reasons.index'],
            //'view-one-income-reasons'    =>['system.income-reasons.show'],
            'delete-income-reasons'      =>['system.income-reasons.destroy','system.income-reasons.show'],
            'create-income-reasons'      =>['system.income-reasons.create','system.income-reasons.store'],
            'update-income-reasons'      =>['system.income-reasons.edit','system.income-reasons.update']
        ]
    ],


    [
        'name' => __('TS Tickets'),
        'description' => __('TS Tickets Permissions'),
        'permissions' => [
            'view-all-tickets'   =>['system.ticket.index'],
            'view-one-ticket'    =>['system.ticket.show'],
            'delete-ticket'      =>['system.ticket.destroy'],
            'create-ticket'      =>['system.ticket.create','system.ticket.store'],
            'Add-comment'      =>['system.ticket.update'],
            'change-status'      =>['system.ticket.change-status'],
            'close-ticket'      =>['close-ticket'],
        ]
    ],

    [
        'name' => __('Campaigns'),
        'description' => __('Campaigns Permissions'),
        'permissions' => [
            'view-all-campaigns'   =>['system.campaign.index'],
            'view-one-campaign'    =>['system.campaign.show'],
            'delete-campaign'      =>['system.campaign.destroy'],
            'create-campaign'      =>['system.campaign.create','system.campaign.store'],
            //'update-campaign'      =>['system.campaign.edit','system.campaign.update'],
        ]
    ],


    [
        'name' => __('Newsletters'),
        'description' => __('Newsletters Permissions'),
        'permissions' => [
            'view-all-newsletters'   =>['system.newsletter.index'],
           // 'view-one-newsletter'    =>['system.newsletter.show'],
            'delete-newsletter'      =>['system.newsletter.destroy'],
            'create-newsletter'      =>['system.newsletter.create','system.newsletter.store'],
            'update-newsletter'      =>['system.newsletter.edit','system.newsletter.update'],
        ]
    ],


    [
        'name' => __('SMS'),
        'description' => __('Sms Permissions'),
        'permissions' => [
            'view-all-SMS'   =>['system.sms.index'],
            'send-SMS'       =>['system.sms.create','system.sms.store'],
            'delete-SMS'     =>['system.sms.destroy'],
        ]
    ],

    [
        'name' => __('Contact Us'),
        'description' => __('Contact Us Permissions'),
        'permissions' => [
            'view-all-messages'   =>['system.contact.index'],
            'view-one-message'    =>['system.contact.show','system.ads.update'],
            'Convert-To-Ticket'      =>['system.contact.to-ticket'],
            'delete-message'      =>['system.contact.destroy'],
            'send-replay'      =>['system.contact.edit','system.contact.update'],
        ]
    ],

    [
        'name' => __('Ads'),
        'description' => __('Ads Permissions'),
        'permissions' => [
            'view-all-ads'   =>['system.ads.index'],
            'view-one-ads'    =>['system.ads.show'],
            'delete-ads'      =>['system.ads.destroy'],
            'create-ads'      =>['system.ads.create','system.ads.store'],
            'update-ads'      =>['system.ads.edit','system.ads.update'],
        ]
    ],

    [
        'name' => __('Slider'),
        'description' => __('Slider Permissions'),
        'permissions' => [
            'view-all-sliders'   =>['system.slider.index'],
            'view-one-slider'    =>['system.slider.show'],
            'delete-slider'      =>['system.slider.destroy'],
            'create-slider'      =>['system.slider.create','system.slider.store'],
            'update-slider'      =>['system.slider.edit','system.slider.update'],
        ]
    ],

    [
        'name' => __('Services'),
        'description' => __('Services Permissions'),
        'permissions' => [
            'view-all-services'    =>['system.service.index'],
            'view-one-services'    =>['system.service.show'],
            'delete-one-service'  =>['system.service.destroy','system.service.show'],
            'create-service'      =>['system.service.create','system.service.store','system.service.remove-image','system.service.image-upload'],
            'update-service'      =>['system.service.edit','system.service.update','system.service.remove-image','system.service.image-upload'],
        ]
    ],

    [
        'name' => __('Packages'),
        'description' => __('Packages Permissions'),
        'permissions' => [
            'view-all-packages'   =>['system.package.index'],
            'view-one-package'    =>['system.package.show'],
            'delete-one-package'  =>['system.package.destroy','system.package.show'],
            'create-package'      =>['system.package.create','system.package.store','system.package.remove-image','system.package.image-upload'],
            'update-package'      =>['system.package.edit','system.package.update','system.package.remove-image','system.package.image-upload'],
        ]
    ],

    [
        'name' => __('Client Packages'),
        'description' => __('Client Packages Permissions'),
        'permissions' => [
            'view-all-client-packages'   =>['system.client-package.index'],
            'view-client-package'    =>['system.client-package.show'],
            'delete-client-package'  =>['system.client-package.destroy','system.client-package.show'],
            'create-client-package'      =>['system.client-package.create','system.client-package.store'],
//            'update-client-package'      =>['system.client-package.edit','system.client-package.update'],
        ]
    ],

    [
        'name' => __('Pages'),
        'description' => __('Pages Permissions'),
        'permissions' => [
            'view-all-pages'    =>['system.page.index'],
            'view-one-pages'    =>['system.page.show'],
            'delete-one-client'  =>['system.page.destroy'],
            'create-page'      =>['system.page.create','system.page.store','system.page.remove-image','system.page.image-upload'],
            'update-page'      =>['system.page.edit','system.page.update'],
        ]
    ],

    [
        'name' => __('Calendar'),
        'description' => __('Calendar Permissions'),
        'permissions' => [
            'manage-calendar' => [
                'system.calendar.index',
                'system.calendar.ajax',
                'system.calendar.show',
                'system.calendar.store',
                'system.calendar.delete'
            ],
        ]
    ],

    [
        'name' => __('Owners & Renters'),
        'description' => __('Owners & Renters Permissions'),
        'permissions' => [
            'view-all-data'    =>['system.both.index'],
            'view-one-client'    =>['system.both.show'],
            'delete-client'  =>['system.both.destroy'],
            'block-client'  =>['system.both.block'],
            'create-client'      =>['system.both.create','system.both.store'],
            'update-client'      =>['system.both.edit','system.both.update'],

        ]
    ],

    [
        'name' => __('Owners'),
        'description' => __('Owners Permissions'),
        'permissions' => [
            'view-all-owners'    =>['system.owner.index'],
            'view-one-owner'    =>['system.owner.show'],
            'delete-owner'  =>['system.owner.destroy'],
            'block-owner'  =>['system.owner.block'],
            'create-owner'      =>['system.owner.create','system.owner.store'],
            'update-owner'      =>['system.owner.edit','system.owner.update'],

        ]
    ],


    [
        'name' => __('Renters'),
        'description' => __('Renters Permissions'),
        'permissions' => [
            'view-all-renters'    =>['system.renter.index'],
            'view-one-renter'    =>['system.renter.show'],
            'delete-renter'  =>['system.renter.destroy'],
            'block-renter'  =>['system.renter.block'],
            'create-renter'      =>['system.renter.create','system.renter.store'],
            'update-renter'      =>['system.renter.edit','system.renter.update'],
//            'client-manage-all'  =>['client-manage-all']

        ]
    ],

    [
        'name' => __('Properties'),
        'description' => __('Property Permissions'),
        'permissions' => [
            'view-all-property'    => ['system.property.index'],
            'view-one-property'    => ['system.property.show'],
            'delete-one-property'  => ['system.property.destroy'],
            'create-property'      => ['system.property.create','system.property.store','system.property.remove-image','system.property.image-upload'],
            'update-property'      => ['system.property.edit','system.property.update'],
            'publish-property'      => ['system.property.publish'],
//            'upload-excel'         => ['system.property.upload-excel','system.property.upload-excel-store'],
            'download-property-excel'         => ['download-property-excel'],
//            'property-manage-all'  => ['property-manage-all']
        ]
    ],

    [
        'name' => __('Requests'),
        'description' => __('Request Permissions'),
        'permissions' => [
            'view-all-request'    =>['system.request.index'],
            'view-one-request'    =>['system.request.show'],
            'delete-one-request'  =>['system.request.destroy','system.request.show'],
            'create-request'      =>['system.request.create','system.request.store'],
            'update-request'      =>['system.request.edit','system.request.update'],
//            'share-request'      =>['system.request.share'],
//            'close-share-request'=>['system.request.close-share'],
            'download-request-excel'         => ['download-request-excel'],
//            'request-manage-all'=> ['request-manage-all']
        ]
    ],


    [
        'name' => __('Importer'),
        'description' => __('Importer Permissions'),
        'permissions' => [
            'view-all-importer'    =>['system.importer.index'],
            'view-one-importer'    =>['system.importer.show'],
            'view-staff-importer'    =>['system.importer.staff'],
            'distribute-to-staff'    =>['system.importer.distribute'],
            //'delete-one-importer'  =>['system.importer.destroy'],
            'create-importer'      =>['system.importer.create','system.importer.store'],
            'update-importer'      =>['system.importer.edit','system.importer.update'],
            'importer-manage-all' => ['importer-manage-all']
        ]
    ],

    [
        'name' => __('Calls'),
        'description' => __('Calls Permissions'),
        'permissions' => [
            'view-all-call'    =>['system.call.index'],
            'view-one-call'    =>['system.call.show'],
            //'delete-one-call'  =>['system.call.destroy'],
            'create-call'      =>['system.call.create','system.call.store'],
            'update-call'      =>['system.call.edit','system.call.update'],
            'call-manage-all'  =>['call-manage-all']

        ]
    ],

    [
        'name' => __('Locations'),
        'description' => __('Area Permissions'),
        'permissions' => [
            'view-all-area'    =>['system.area.index'],
            'view-one-area'    =>['system.area.show'],
            //'delete-one-area'  =>['system.area.destroy'],
            'create-area'      =>['system.area.create','system.area.store'],
            'update-area'      =>['system.area.edit','system.area.update']
        ]
    ],


    [
        'name' => __('Area Type'),
        'description' => __('Area Type Permissions'),
        'permissions' => [
            'view-all-area-type'    =>['system.area-type.index'],
            'view-one-area-type'    =>['system.area-type.show'],
            //'delete-one-area-type'  =>['system.area-type.destroy'],
            'create-area-type'      =>['system.area-type.create','system.area-type.store'],
            'update-area-type'      =>['system.area-type.edit','system.area-type.update']
        ]
    ],

    [
        'name' => __('Staff'),
        'description' => __('Staff Permissions'),
        'permissions' => [
            'view-all-staff'    =>['system.staff.index'],
            'view-one-staff'    =>['system.staff.show'],
            // 'delete-one-staff'  =>['system.staff.destroy'],
            'create-staff'      =>['system.staff.create','system.staff.store'],
            'update-staff'      =>['system.staff.edit','system.staff.update']
        ]
    ],

    [
        'name' => __('Permission Group'),
        'description' => __('Permission Group Permissions'),
        'permissions' => [
            'view-all-permission-group'    =>['system.permission-group.index'],
            'view-one-permission-group'    =>['system.permission-group.show'],
            // 'delete-one-permission-group'  =>['system.permission-group.destroy'],
            'create-permission-group'      =>['system.permission-group.create','system.permission-group.store'],
            'update-permission-group'      =>['system.permission-group.edit','system.permission-group.update']
        ]
    ],

//    [
//        'name' => __('Phone Numbers'),
//        'description' => __('Show Phone Numbers'),
//        'permissions' => [
//            'show-all-phones'  =>['show-all-phones'],
//        ]
//    ],





    [
        'name' => __('Property Type'),
        'description' => __('Property Type Permissions'),
        'permissions' => [
            'view-all-property-type'    =>['system.property-type.index'],
            'view-one-property-type'    =>['system.property-type.show'],
            //'delete-one-property-type'  =>['system.property-type.destroy'],
            'create-property-type'      =>['system.property-type.create','system.property-type.store'],
            'update-property-type'      =>['system.property-type.edit','system.property-type.update']
        ]
    ],

    [
        'name' => __('Property Features'),
        'description' => __('Property Features Permissions'),
        'permissions' => [
            'view-all-property-features'    =>['system.property-features.index'],
            //'view-one-property-features'    =>['system.property-features.show'],
            'delete-one-property-features'  =>['system.property-features.destroy','system.property-features.show'],
            'create-property-features'      =>['system.property-features.create','system.property-features.store'],
            'update-property-features'      =>['system.property-features.edit','system.property-features.update']
        ]
    ],


//    [
//        'name' => __('Data Source'),
//        'description' => __('Data Source Permissions'),
//        'permissions' => [
//            'view-all-data-source'    =>['system.data-source.index'],
//            'view-one-data-source'    =>['system.data-source.show'],
//            //'delete-one-data-source'  =>['system.data-source.destroy'],
//            'create-data-source'      =>['system.data-source.create','system.data-source.store'],
//            'update-data-source'      =>['system.data-source.edit','system.data-source.update']
//        ]
//    ],

    [
        'name' => __('Purpose'),
        'description' => __('Purpose Permissions'),
        'permissions' => [
            'view-all-purpose'    =>['system.purpose.index'],
            'view-one-purpose'    =>['system.purpose.show'],
            //'delete-one-purpose'  =>['system.purpose.destroy'],
            'create-purpose'      =>['system.purpose.create','system.purpose.store'],
            'update-purpose'      =>['system.purpose.edit','system.purpose.update']
        ]
    ],

    [
        'name' => __('Call Purpose'),
        'description' => __('Call Purpose Permissions'),
        'permissions' => [
            'view-all-call-purpose'    =>['system.call-purpose.index'],
            'view-one-call-purpose'    =>['system.call-purpose.show'],
            //'delete-one-call-purpose'  =>['system.call-purpose.destroy'],
            'create-call-purpose'      =>['system.call-purpose.create','system.call-purpose.store'],
            'update-call-purpose'      =>['system.call-purpose.edit','system.call-purpose.update']
        ]
    ],



    [
        'name' => __('Call Status'),
        'description' => __('Call Status Permissions'),
        'permissions' => [
            'view-all-call-status'    =>['system.call-status.index'],
            'view-one-call-status'    =>['system.call-status.show'],
            //'delete-one-call-status'  =>['system.call-status.destroy'],
            'create-call-status'      =>['system.call-status.create','system.call-status.store'],
            'update-call-status'      =>['system.call-status.edit','system.call-status.update']
        ]
    ],



    [
        'name' => __('Setting'),
        'description' => __('Setting Permissions'),
        'permissions' => [
            'manage-setting'    =>['system.setting.index','system.setting.update']
        ]
    ],


//    [
//        'name' => __('Activity Log'),
//        'description' => __('Activity Log'),
//        'permissions' => [
//            'view-activity-log'=>['system.activity-log.index'],
//            'view-one-activity-log'=>['system.activity-log.show'],
//        ]
//    ],

//    [
//        'name' => __('Auth Sessions'),
//        'description' => __('Auth Sessions'),
//        'permissions' => [
//            'view-auth-session'=>['system.staff.auth-sessions'],
//            'delete-auth-session'=>['system.staff.delete-auth-sessions'],
//        ]
//    ],


    


];