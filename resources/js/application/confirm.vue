<template>
  <div class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header" style="border-bottom: none">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
          </button>
          <div class="h4 modal-title">{{text}}</div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" @click="doConfirm">{{okText}}</button>
          <button type="button" class="btn btn-default" @click="doCancel">{{cancelText}}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
    data: function () {
        return {
            text: '',
            okText: 'OK',
            cancelText: 'Отмена',
            deferred: null,
        };
    },
    methods: {
        confirm: function (text, okText, cancelText) {
            this.text = text + '';
            this.okText = okText || 'OK';
            this.cancelText = cancelText || 'Отмена';
            this.promise = new $.Deferred();
            $(this.$el).modal('show');
            return this.promise;
        },
        doConfirm: function () {
            $(this.$el).modal('hide');
            this.promise.resolve(true);
        },
        doCancel: function () {
            $(this.$el).modal('hide');
            this.promise.reject(false);
        },
    }
}
</script>