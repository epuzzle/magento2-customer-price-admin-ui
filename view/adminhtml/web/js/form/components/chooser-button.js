define([
    'uiComponent',
    'mageUtils'
], function (Component, utils) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'EPuzzle_CustomerPriceAdminUi/form/components/chooser-button',
            title: '',
            scopeLabel: '[global]',
            additionalClasses: '',
            uid: utils.uniqueid(),
            require: false,
            requireMessage: 'The entity is required.',
            options: [],
            entityId: '',
            storeId: '',
            listens: {
                '${ $.provider }:data.validate': 'onValidate'
            },
        },

        /**
         * Require message flag
         */
        isShowRequireMessage: false,

        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();
            if (this.require) {
                this.additionalClasses(
                    this.additionalClasses() + ' _required'
                )
            }
            return this;
        },

        /**
         * @inheritDoc
         */
        initObservable: function () {
            return this._super()
                .observe([
                    'entityId',
                    'storeId',
                    'options',
                    'additionalClasses',
                    'isShowRequireMessage'
                ]);
        },

        /**
         * Updates target entity from external listing
         *
         * @param {Object} params
         * @return {void}
         */
        updateData: function (params) {
            this.isShowRequireMessage(false);

            if (params.entityId) {
                this.entityId(params.entityId);
            }

            if (params.options) {
                this.options(params.options);
            }
        },

        /**
         * Clear selected data
         *
         * @return {void}
         */
        clearData: function () {
            this.isShowRequireMessage(false);
            this.entityId('');
            this.options([]);
        },

        /**
         * Validate the entity before submit the form
         *
         * @return {void}
         */
        onValidate: function () {
            if (this.require && !this.entityId()) {
                this.isShowRequireMessage(true);
                this.source.set('params.invalid', true);
            } else {
                this.isShowRequireMessage(false);
            }
        }
    });
});
