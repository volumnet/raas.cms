<template>
  <div class="cookies-notification" :style="{ display: (active ? 'block' : 'none') }">
    <a class="cookies-notification__close" @click="close($event)"></a>
    <div class="cookies-notification__inner">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
    data: function () {
        return {
            active: false,
        };
    },
    mounted: function () {
        if (!Cookie.get('cookies-notification')) {
            this.active = true;
        }
    },
    methods: {
        close: function ($event) {
            Cookie.set(
                'cookies-notification', 
                '1', 
                { expires: 3650, path: '/' }
            );
            this.active = false;
            $event.preventDefault();
        }
    },
};
</script>