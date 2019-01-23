startTwilio();
function startTwilio() {
    const appConfig = {
        accountSid:"AC7572a1439e6746742a55c2275de3034c",
        flexFlowSid:"FO43db8f63dfe6a235a140134d77aa8c43",
         startEngagementOnInit: true,
         componentProps: {
            EntryPoint: {
                tagline: "Get help",
            },
            MainHeader: {
                titleText: "Funda Wande Help",
            }
         },
         colorTheme: {
            baseName: "FundaWande",
            overrides: {
                EntryPoint: {
                        Container: {
                            background: "#ef9d19",
                            color: "#ffffff"
                        }
                },
                PostEngagementCanvas: {
                    DynamicForm: {
                        SubmitButton: {
                            background: "#ef9d19",
                            color: "#ffffff"
                        },
                    }
                },
                PreEngagementCanvas: {
                    Form: {
                        SubmitButton: {
                            background: "#ef9d19",
                            color: "#ffffff"
                        }
                    }
                },
                Chat: {    
                    MessageCanvasTray: {
                        Container: {
                            background: "#ef9d19",
                        }
                    },
                    MessageInput: {
                        Button: {
                            background: "#ef9d19",
                        }
                    },
                    MessageListItem: {
                        FromMe: {
                            Bubble: {
                                color: "#ffffff",
                                background: "#ef9d19"
                            },
                        },
                    },
                },
            }
        }

       
       
    };
    Twilio.Flex.createWebChat(appConfig);
    Twilio.Flex.Actions.invokeAction ("ToggleChatVisibility");
    Twilio.Flex.Actions.invokeAction ("ToggleChatVisibility");
}