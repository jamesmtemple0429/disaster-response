<template>
    <app-layout title="Team Settings">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Team Settings
            </h2>
        </template>

        <div>
            <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <update-team-name-form :team="team" :permissions="permissions" v-if="hasPermission('teams.update') || hasPermission('teams.update.own')" />

                <assigned-regions :team="team" :permissions="permissions" v-if="team.type === 2" />

                <team-member-manager class="mt-10 sm:mt-0"
                            :team="team"
                            :available-roles="availableRoles"
                            :user-permissions="permissions"
                            v-if="hasPermission('teams.members.manage')"
                />

                <template v-if="hasPermission('teams.update')">
                    <jet-section-border />

                    <delete-team-form class="mt-10 sm:mt-0" :team="team" />
                </template>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import DeleteTeamForm from '@/Pages/Teams/Partials/DeleteTeamForm.vue'
    import JetSectionBorder from '@/Jetstream/SectionBorder.vue'
    import TeamMemberManager from '@/Pages/Teams/Partials/TeamMemberManager.vue'
    import UpdateTeamNameForm from '@/Pages/Teams/Partials/UpdateTeamNameForm.vue'
    import AssignedRegions from '@/Pages/Teams/Partials/AssignedRegions.vue'

    export default defineComponent({
        props: [
            'team',
            'availableRoles',
            'permissions',
        ],

        components: {
            AppLayout,
            DeleteTeamForm,
            JetSectionBorder,
            TeamMemberManager,
            UpdateTeamNameForm,
            AssignedRegions
        },

        methods: {
            hasPermission(permission) {
                return this.$page.props.user.permissions.includes(permission) || this.$page.props.user.permissions.includes("*");
            },
        }
    })
</script>
