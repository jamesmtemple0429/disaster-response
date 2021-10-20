<template>
    <jet-form-section @submitted="createTeam">
        <template #title>
            Team Details
        </template>

        <template #description>
            Create a new team to collaborate with others on projects.
        </template>

        <template #form>
            <div class="col-span-6">
                <jet-label value="Team Owner" />

                <div class="flex items-center mt-2">
                    <img class="object-cover w-12 h-12 rounded-full" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name">

                    <div class="ml-4 leading-tight">
                        <div>{{ $page.props.user.name }}</div>
                        <div class="text-sm text-gray-700">{{ $page.props.user.email }}</div>
                    </div>
                </div>
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="name" value="Team Name" />
                <jet-input id="name" type="text" class="block w-full mt-1" v-model="form.name" autofocus />
                <jet-input-error :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <jet-label for="type" value="Team Type" />

                <jet-select id="type"
                            class="mt-1 block w-full"
                            v-model="form.type"
                >
                    <option value="1">Regional</option>
                    <option value="2">Dispatch Center</option>
                    <option value="3" v-if="$page.props.user.current_team.type === 4">Divisional</option>
                </jet-select>

                <jet-input-error :message="form.errors.type" class="mt-2" />
            </div>

            
             <div class="col-span-6 sm:col-span-4" v-if="form.type <= 2">
                <jet-label for="type" value="Divisional Team"/>

                <jet-select id="type"
                            class="mt-1 block w-full"
                            v-model="form.divisionID"
                >
                    <option value="0" v-if="$page.props.user.current_team.type === 4">Select a Division...</option>
                    <option v-for="team in divisionalTeams" :key="team.id" :value="team.id">{{ team.name }}</option>
                </jet-select>

                <jet-input-error :message="form.errors.divisionID" class="mt-2" />
            </div>

            
             <div class="col-span-6 sm:col-span-4" v-if="form.type != 4">
                <jet-label for="type" value="National Team" />

                <jet-select id="type"
                            class="mt-1 block w-full"
                            v-model="form.nationalID"
                >
                    <option v-for="team in nationalTeams" :value="team.id" :key="team.id">{{ team.name }}</option>
                </jet-select>

                <jet-input-error :message="form.errors.nationalID" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSelect from '@/Jetstream/Select.vue'

    let _ = require('underscore');

    export default defineComponent({
        components: {
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSelect
        },

        created() {
            axios.get("/api/teams")
                .then((response) => {
                    this.allTeams = response.data;

                    this.nationalTeams = _.filter(response.data, (team) => {
                        return team.type === 4;
                    });

                    this.form.nationalID = _.first(this.nationalTeams).id;


                    if(this.$page.props.user.current_team.type === 4) {
                        this.divisionalTeams = _.filter(response.data, (team) => {
                            return team.type === 3;
                        });
                    } else {
                        this.divisionalTeams = _.filter(response.data, (team) => {
                            return team.id === this.$page.props.user.current_team.id;
                        });

                        this.form.divisionID = this.divisionalTeams[0].id;
                    }

                });
        },

        data() {
            return {
                divisionalTeams: [],
                nationalTeams: [],
                form: this.$inertia.form({
                        name: '',
                        type: 1,
                        divisionID: 0,
                        nationalID: 1
                }),
            }
        },

        methods: {
            createTeam() {
                this.form.post(route('teams.store'), {
                    errorBag: 'createTeam',
                    preserveScroll: true
                });
            },
        },
    })
</script>
