<template>
    <v-dialog v-model="show" max-width="600px">
        <v-card>
            <v-card-title>
                <span class="headline" v-if="isEdited">{{
                    $t("empleados.edit_empleado")
                }}</span>
                <span class="headline" v-else>{{ $t("empleados.add_empleado") }}</span>
            </v-card-title>

            <v-card-text>
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-container>
                        <p class="px-3 py-3 rounded-lg blue lighten-4">{{ $t('general.mandatory_fields') }}</p>
                        <v-row>
                            <v-col cols="12" sm="12">
                                <v-text-field v-model="empleado.nombre" :label="$t('empleados.full_name') + ' *'"
                                    autofocus :rules="stringRules"
                                    :placeholder="$t('empleados.name_placeholder')"></v-text-field>
                                <v-text-field ref="email" v-model="empleado.email" :rules="emailRules"
                                    :label="$t('empleados.email') + ' *'"
                                    :placeholder="$t('empleados.email')"></v-text-field>
                                <div class="d-flex align-center mt-2 ">
                                    <p class="mb-0 mr-3">{{ $t('empleados.sex') + ' *' }}</p>
                                    <v-radio-group v-model="empleado.sexo" :rules="requiredRule">
                                        <v-radio key="empleadoMaleOption" :label="$t('general.male')"
                                            value="M"></v-radio>
                                        <v-radio key="empleadoFemaleOption" :label="$t('general.female')"
                                            value="F"></v-radio>
                                    </v-radio-group>
                                </div>
                                <v-select :items="areas" v-model="empleado.area_id" item-value="id" item-text="nombre"
                                    :label="$t('empleados.area') + ' *'" :rules="requiredRule"></v-select>
                                <v-textarea v-model="empleado.descripcion" :label="$t('empleados.description') + ' *'"
                                    :rules="textRules"
                                    :placeholder="$t('empleados.description_placeholder')"></v-textarea>

                                <v-checkbox v-model="empleado.boletin"
                                    :label="$t('empleados.accept_to_receive_newsletter')">
                                </v-checkbox>

                                <div class="d-flex align-center mt-2 ">
                                    <p class="mb-0 mr-3">{{ $t('empleados.roles') + ' *' }}</p>
                                    <div>
                                        <v-checkbox class="mt-0" v-for="(role, roleIndex) in empleadoRoles"
                                            :key="'role-' + roleIndex" v-model="empleado.roles" :label="role.nombre"
                                            :value="role.id" :rules="roleRule"></v-checkbox>
                                    </div>
                                </div>
                            </v-col>
                        </v-row>
                    </v-container>
                </v-form>
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" dark @click="submit">{{
                    $t("general.save")
                }}</v-btn>
                <v-btn @click.stop="show = false">{{
                    $t("general.cancel")
                }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "EmpleadoForm",
    props: {
        form: {
            type: Object,
        },
        empleado: Object,
        empleadoRoles: Array,
        areas: Array,
        visible: Boolean,
        isEdited: Boolean,
    },

    data() {
        return {
            valid: true,
            showEye: false,
            requiredRule: [(value) => !!value || this.$t("general.required")],
            roleRule: [(value) => value.length > 0 || this.$t("general.required")],
            stringRules: [
                (value) => !!value || this.$t("general.required"),
                (value) =>
                    (value && value.length <= 255) || this.$t("general.max_255_characters"),
            ],
            textRules: [
                (value) => !!value || this.$t("general.required"),
                (value) =>
                    (value && value.length <= 10000) || this.$t("general.max_10000_characters"),
            ],
            emailRules: [
                (value) => !!value || this.$t("general.required"),
                (value) =>
                    (value && value.length <= 255) || this.$t("general.max_255_characters"),
                (value) => {
                    if (!value) return true;
                    const trimmed = value.trim();
                    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    return pattern.test(trimmed) || this.$t("general.email_not_valid");
                },
            ],
        };
    },

    watch: {
        show(val) {
            !val && this.$refs.form.resetValidation();
        },
    },

    computed: {
        show: {
            get() {
                return this.visible;
            },
            set(value) {
                if (!value) {
                    this.$emit("close");
                }
            },
        },
    },

    methods: {
        async submit() {
            if (this.$refs.form.validate()) {
                if (this.isEdited) {
                    await axios
                        .put(`/api/updateEmpleado/${this.empleado.id}`, this.empleado, {
                            headers: {
                                Authorization:
                                    "Bearer " +
                                    this.$store.state.tokenData.user
                                        .access_token,
                            },
                        })
                        .then((response) => {
                            if (response.data.isError) {
                                this.$emit(
                                    "snackMessage",
                                    response.data.message,
                                    "error"
                                );
                                this.$refs.email.focus();
                            } else {
                                this.$emit("updateEmpleado", response.data);
                                this.$emit(
                                    "snackMessage",
                                    "empleados.empleado_saved",
                                    "success"
                                );
                                this.$emit("close");
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                            this.$emit(
                                "snackMessage",
                                "general.error",
                                "error"
                            );
                        });
                } else {
                    await axios
                        .post(`/api/storeEmpleado`, this.empleado, {
                            headers: {
                                Authorization:
                                    "Bearer " +
                                    this.$store.state.tokenData.user
                                        .access_token,
                            },
                        })
                        .then((response) => {
                            if (response.data.isError) {
                                this.$refs.email.focus();
                                this.$emit(
                                    "snackMessage",
                                    response.data.message,
                                    "error"
                                );
                            } else {
                                this.$emit("addEmpleado", response.data);
                                this.$emit(
                                    "snackMessage",
                                    "empleados.empleado_added",
                                    "success"
                                );
                                this.$emit("close");
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                            this.$emit(
                                "snackMessage",
                                "general.error",
                                "error"
                            );
                        });
                }
            }
        },
    },
};
</script>
