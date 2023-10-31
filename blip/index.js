
import * as BlipSdk from 'blip-sdk';
import * as WebSocketTransport from 'lime-transport-websocket'

// Put your identifier and access key here
let IDENTIFIER = 'step';
let ACCESS_KEY = 'Key c3RlcDpqbHppZkJodE1OQk50NW5VOVNLWg==';

// Create a client instance passing the identifier and accessKey of your chatbot
let client = new BlipSdk.ClientBuilder()
    .withIdentifier(IDENTIFIER)
    .withAccessKey(ACCESS_KEY)
    .withTransportFactory(() => new WebSocketTransport())
    .build();

// Connect with server asynchronously
// Connection will occurr via websocket on 8081 port.
client.connect() // This method return a 'promise'.
    .then(function(session) {
        // Connection success. Now is possible send and receive envelopes from server. */
        console.log('Application started. Press Ctrl + c to stop.')


        client.connect()
    .then(function(session) {
        // After connection is possible send messages
        client.sendMessage({
            id: Lime.Guid(),
            type: "application/vnd.lime.collection+json",
            to: "128271320123982@messenger.gw.msging.net",
            content: {
                itemType: "text/plain",
                items: [
                    "Text 1",
                    "Text 2",
                    "Text 3"
                ]
            }
        });

        
    });

    
    })
    .catch(function(err) { /* Connection failed. */  console.log("Erro ao conectar"); });