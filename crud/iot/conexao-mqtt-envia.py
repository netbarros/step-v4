import paho.mqtt.client as mqtt

import time
import random
from datetime import datetime

# Configurações do MQTT
broker_address = "34.138.248.32"
broker_port = 1883
topic = "Altus/TESTE"

# Criando o cliente
client = mqtt.Client()

try:
    # Conectando ao broker
    client.connect(broker_address, broker_port)
    
    start_time = time.time()
    while True:  # Loop infinito
        if time.time() - start_time > 60:  # Roda o loop por 1 minuto
            break

        # Gera a mensagem com valores aleatórios
        timestamp = datetime.now().strftime('%Y-%m-%d-%H-%M-%S')
        values = " ".join([f"1,2,{random.uniform(40.0, 50.0):.2f}" for _ in range(5)])
        message = f"DT#{timestamp} {values}"

        # Publica a mensagem
        client.publish(topic, message)
        print(f"Mensagem enviada: {message}")

        time.sleep(1)  # Pausa por 1 segundo

    client.disconnect()  # Desconecta do broker

except Exception as e:
    print("Exceção: ", str(e))
