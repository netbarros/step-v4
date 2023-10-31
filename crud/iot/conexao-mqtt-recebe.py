import time
import paho.mqtt.client as mqtt
import json

# Configurações do MQTT
broker_address = "34.138.248.32"
broker_port = 1883
topic = "Altus/TESTE"

# Callback que é chamada quando o cliente se conecta ao broker
def on_connect(client, userdata, flags, rc):
    if rc == 0:
        print("Conectado ao broker")
        client.subscribe(topic)  # Se inscreve no tópico
    else:
        print("Erro na conexão com o broker, retorno do código: ", rc)

# Função para parsear a mensagem
def parse_message(message):
    try:
        # Decodifica a mensagem JSON
        message_obj = json.loads(message)
        timestamp = message_obj['timestamp']
        values = message_obj['values']

        for reading in values:
            plc_id, equipment_id, value = reading.split(',')
            print(f"Timestamp: {timestamp}, ID PLC: {plc_id}, ID do Equipamento: {equipment_id}, Valor: {value}")

    except json.JSONDecodeError:
        print(f"AVISO: Mensagem inválida ignorada: '{message}'")

# Callback que é chamada quando uma mensagem PUBLISH é recebida do broker
def on_message(client, userdata, message):
    print(f"\nMensagem recebida no tópico {message.topic}")
    message_decoded = message.payload.decode()
    parse_message(message_decoded)

# Criando o cliente
client = mqtt.Client()

# Atribuindo as funções de callback ao cliente
client.on_connect = on_connect
client.on_message = on_message

try:
    # Conectando ao broker
    client.connect(broker_address, broker_port)

    while True:  # Loop infinito
        client.loop_start()  # Começa o loop de rede em segundo plano
        time.sleep(10)  # Pausa o script por 10 segundos
        client.loop_stop()  # Para o loop de rede em segundo plano

except Exception as e:
    print("Exceção: ", str(e))
