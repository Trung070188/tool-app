<template>
    <label class="switch"><input ref="input" v-model="isChecked" @change="toggle()" type="checkbox" :disabled="disabled"> <span
        class="slider round"></span></label>
</template>

<script>

export default {
    name: "SwitchButton",
    data: function () {
        const v = typeof this.modelValue === 'boolean' ? this.modelValue : parseInt(this.modelValue);
        return {
            isChecked: !!v,
            watchIgnored: true,
        }
    },
    mounted: function () {
    },
    props: ['modelValue', 'input_id','disabled'],
    watch: {
        modelValue: function (v) {
            if ( this.watchIgnored) {
                this.watchIgnored = false;
                return;
            }

            this.isChecked = v;
        }
    },
    methods: {
        toggle: function () {
            this.watchIgnored = true;
            this.$emit('update:modelValue', this.$refs.input.checked ? 1 : 0);

        }
    }
}
</script>

<style scoped>
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

input:focus + .slider {
    box-shadow: 0 0 1px #2196f3;
}

input:checked + .slider {
    background-color: #2196f3;
}

.slider.round {
    border-radius: 34px;
}

.switch input {
    display: none;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    -webkit-transition: .4s;
    transition: .4s;
}

input:checked + .slider:before {
    -webkit-transform: translateX(26px);
    -ms-transform: translateX(26px);
    transform: translateX(26px);
}

.slider.round:before {
    border-radius: 50%;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: #fff;
    -webkit-transition: .4s;
    transition: .4s;
}

</style>
