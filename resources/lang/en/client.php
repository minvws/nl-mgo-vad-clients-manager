<?php

declare(strict_types=1);

return [
    'model_plural' => "Clients",
    'organisation' => "Organisation",
    'organisation_id' => "Organisation",
    'id' => "Client ID",
    'owner_organisation' => [
        'name' => "Organisation",
        'main_contact_email' => "Main contact email",
    ],
    'fqdn' => "FQDN",
    'token_endpoint_auth_method' => "Token Endpoint Auth Method",
    'token_endpoint_auth_method_none' => "None",
    'token_endpoint_auth_method_client_secret_post' => "Client Secret Post",
    'redirect_uris' => "Redirect URIs",
    'redirect_uris_help' => "Add the redirect URIs you want to use for this client here.",
    'active' => "Active",
    'created_at' => "Created at",
    'updated_at' => "Updated at",
    'created_at_header' => "Created",
    'updated_at_header' => "Updated",
    'create' => "Create client",
    'created_successfully' => "Client created",
    'edit' => "Edit client",
    'updated_successfully' => "Client updated",
    'actions' => 'Actions',
    'search_placeholder' => "Search by ID, Organisation name, Main contact email or FQDN.",
    'search_organisation' => "Search organisation...",
    'active_filter' => [
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],
    'generated_mail' => [
        'subject' => 'VAD Client Credentials Generated',
        'greeting' => 'Hello!',
        'generated_message' => 'Your VAD Client credentials have been generated for the following application:',
        'store_secretly' => 'Please ensure that these client credentials are stored securely and kept secret',
        'usage' => 'You can use these credentials to authenticate your application with the VAD'
    ]
];