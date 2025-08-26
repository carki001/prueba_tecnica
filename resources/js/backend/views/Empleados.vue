<template>
    <div>
        <header>
            <v-container>
                <v-row no-gutters>
                    <h1 class="font-weight-light headline">
                        {{ $t("title.empleados") }}
                    </h1>
                    <v-spacer></v-spacer>
                    <v-lazy v-if="isEmpleadosLoaded" transition="fade-transition">
                        <v-text-field v-model="search" append-icon="mdi-magnify" :label="searchLabel" single-line dense
                            hide-details style="max-width: 200px" class="mr-5 pr-5"></v-text-field>
                    </v-lazy>
                    <v-lazy v-if="isEmpleadosLoaded" transition="fade-transition">
                        <v-btn color="success" dark small @click.stop="prepareEmpleado">{{ $t("general.add") }}</v-btn>
                    </v-lazy>
                </v-row>
            </v-container>
        </header>
        <section>
            <v-container class="pt-0">
                <v-sheet class="px-3 pt-3 pb-3" v-if="!empleados.length">
                    <v-skeleton-loader class="mx-auto" type="table"></v-skeleton-loader>
                </v-sheet>
                <v-card v-else>
                    <v-data-table :headers="headers" :items="empleados" :search="search" :items-per-page="15" dense>
                        <template v-slot:header.action="{ header }">
                            <v-icon small class="mr-1">mdi-cog</v-icon>
                            {{ header.text }}
                        </template>
                        <template v-slot:header.nombre="{ header }">
                            <v-icon small class="mr-1">mdi-account</v-icon>
                            {{ header.text }}
                        </template>
                        <template v-slot:header.email="{ header }">
                            <v-icon small class="mr-1">mdi-at</v-icon>
                            {{ header.text }}
                        </template>
                        <template v-slot:header.sexo="{ header }">
                            <v-icon small class="mr-1">mdi-gender-male-female</v-icon>
                            {{ header.text }}
                        </template>
                        <template v-slot:header.area_name="{ header }">
                            <v-icon small class="mr-1">mdi-briefcase</v-icon>
                            {{ header.text }}
                        </template>
                        <template v-slot:header.boletin="{ header }">
                            <v-icon small class="mr-1">mdi-email</v-icon>
                            {{ header.text }}
                        </template>

                        <template v-slot:item.action="{ item }">
                            <div style="display: flex">
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                        <v-icon v-tr-class-on-hover="'blue'" size="20px" color="blue" class="mr-2"
                                            @click="editEmpleado(item)" v-on="on">mdi-pencil-outline</v-icon>
                                    </template>
                                    <span>{{ $t("general.edit") }}</span>
                                </v-tooltip>
                                <v-tooltip top>
                                    <template v-slot:activator="{ on }">
                                        <v-icon size="22px" color="red" v-tr-class-on-hover="'red'" class="mr-2"
                                            @click="confirmDelete(item)" v-on="on">mdi-delete-outline</v-icon>
                                    </template>
                                    <span>{{ $t("general.delete") }}</span>
                                </v-tooltip>
                            </div>
                        </template>
                        <template v-slot:item.boletin="{ item }">
                            <span>{{ item.boletin === true ? $t("general.yes") : $t("general.no") }}</span>
                        </template>

                    </v-data-table>
                </v-card>

                <EmpleadoForm :empleado="selectedEmpleado" :empleadoRoles="empleadoRoles" :visible="dialog.empleadoForm"
                    :isEdited="isEmpleadoEdited" :areas="areas" @addEmpleado="addEmpleado"
                    @updateEmpleado="updateEmpleado" @close="dialog.empleadoForm = false"
                    @snackMessage="snackMessage" />

                <ConfirmationDialog :name="selectedEmpleado.nombre" :text="'empleados.delete_confirmation'"
                    v-if="dialog.confirmation" @confirm="deleteEmpleado" @cancel="cancelDelete" />

                <SnackMessage ref="SnackMessage" />
            </v-container>
        </section>
    </div>
</template>

<script>
import EmpleadoForm from "../components/EmpleadoForm";
import ConfirmationDialog from "../components/ConfirmationDialog";
import SnackMessage from "../components/SnackMessage";

export default {
    data() {
        return {
            empleados: [],
            areas: [],
            isEmpleadosLoaded: false,
            search: "",
            dialog: {
                confirmation: false,
                empleadoForm: false,
            },

            selectedEmpleado: {
                boletin: false,
                roles: [],
            },
            defaultEmpleado: {
                boletin: false,
                roles: [],
            },
            isEmpleadoEdited: false,
            empleadoRoles: [],
        };
    },

    components: {
        EmpleadoForm,
        ConfirmationDialog,
        SnackMessage,
    },

    computed: {
        headers() {
            return [
                {
                    text: this.$t("general.actions"),
                    value: "action",
                    sortable: false,
                },
                {
                    text: this.$t("empleados.name"),
                    align: "left",
                    sortable: false,
                    value: "nombre",
                },
                {
                    text: this.$t("empleados.email"),
                    align: "left",
                    sortable: false,
                    value: "email",
                },
                {
                    text: this.$t("empleados.sex"),
                    align: "left",
                    sortable: false,
                    value: "sexo",
                },
                {
                    text: this.$t("empleados.area"),
                    align: "left",
                    sortable: false,
                    value: "area_name",
                },
                {
                    text: this.$t("empleados.newsletter"),
                    align: "left",
                    sortable: false,
                    value: "boletin",
                },
            ];
        },
        searchLabel() {
            return this.$t("general.search");
        },
    },

    created() {
        this.getEmpleados();
        this.getAreas();
        this.getEmpleadoRoles();
    },


    methods: {
        snackMessage(msg, type) {
            this.$refs.SnackMessage.showMessage(msg, type);
        },

        async getEmpleados() {
            await axios
                .get("/api/empleadoList", {
                    headers: {
                        Authorization:
                            "Bearer " +
                            this.$store.state.tokenData.user.access_token,
                    },
                })
                .then((response) => {
                    this.empleados = response.data;
                    this.isEmpleadosLoaded = true;
                })
                .catch((error) => {
                    console.log(error.response.data);
                });
        },

        async getAreas() {
            await axios
                .get("/api/areaList", {
                    headers: {
                        Authorization:
                            "Bearer " +
                            this.$store.state.tokenData.user.access_token,
                    },
                })
                .then((response) => {
                    this.areas = response.data;
                })
                .catch((error) => {
                    console.log(error.response.data);
                });
        },

        async getEmpleadoRoles() {
            await axios
                .get("/api/empleadoRoles", {
                    headers: {
                        Authorization:
                            "Bearer " +
                            this.$store.state.tokenData.user.access_token,
                    },
                })
                .then((response) => {
                    this.empleadoRoles = response.data;

                })
                .catch((error) => {
                    console.log(error.response.data);
                });
        },


        prepareEmpleado() {
            var newObject = JSON.stringify(this.defaultEmpleado);
            this.selectedEmpleado = JSON.parse(newObject);

            this.dialog.empleadoForm = true;
            this.isEmpleadoEdited = false;
        },
        addEmpleado(empleado) {
            this.empleados.push(empleado);
        },
        editEmpleado(item) {
            this.selectedEmpleado = Object.assign({}, item);
            this.dialog.empleadoForm = true;
            this.isEmpleadoEdited = true;
        },
        updateEmpleado(empleado) {
            let index = this.empleados.findIndex((item) => item.id == empleado.id);

            if (index > -1) {
                this.empleados.splice(index, 1, empleado);
            }

        },
        confirmDelete(item) {
            this.selectedEmpleado = JSON.parse(JSON.stringify(item));
            this.dialog.confirmation = true;
        },
        cancelDelete() {
            this.dialog.confirmation = false;
        },
        async deleteEmpleado() {
            this.dialog.confirmation = false;
            let index = this.empleados.findIndex((item) => item.id == this.selectedEmpleado.id)

            await axios
                .delete(`/api/deleteEmpleado/${this.selectedEmpleado.id}`, {
                    headers: {
                        Authorization:
                            "Bearer " +
                            this.$store.state.tokenData.user.access_token,
                    },
                })
                .then((response) => {
                    var newObject = JSON.stringify(this.defaultEmpleado);
                    this.selectedEmpleado = JSON.parse(newObject);

                    if (index > -1) {
                        this.empleados.splice(index, 1);
                    }

                    this.$refs.SnackMessage.showMessage(
                        "empleados.deleted_successfully",
                        "success"
                    );
                })
                .catch((error) => {
                    console.log(error);
                    this.$refs.SnackMessage.showMessage(
                        "general.error",
                        "error"
                    );
                });
        },

    },
};
</script>
