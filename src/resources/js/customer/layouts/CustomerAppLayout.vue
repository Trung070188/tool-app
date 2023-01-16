<template>
    <div class="page ">
        <div>
            <NavBar/>
            <div class="jumps-prevent" style="padding-top: 63px;"></div>
            <SideBar/>
            <div class="jumps-prevent" style="padding-top: 63px;"></div>
        </div>

        <Main/>

        <Footer/>
    </div>
</template>

<script>

    import registry from "../../registry_customer";
    import {botWarn} from "../../utils";
    import SideBar from "../includes/SideBar";
    import NavBar from "../includes/NavBar";
    import Footer from "../includes/Footer";
    import ModalSelectDB from "../../components/ModalSelectDB";

    const componentName = window.$componentName;

    if (!componentName) {
        botWarn('Customer','You need to set component in controller');
    }

    console.log(componentName);

    const component = registry[componentName];

    export default {
        name: "CustomerAppLayout",
        components: {ModalSelectDB, Footer, NavBar, SideBar, Main: component},
        mounted() {
            $('#global-loader').hide();
        },
        data() {
            return {}
        }
    }
</script>

<style scoped>

</style>
