#!/usr/bin/env python3
import paho.mqtt.client as mqtt
import mysql.connector
import json
import logging

# Configurações do MQTT
broker_address = "34.138.248.32"
broker_port = 1883
topic = "Altus/TESTE"

# Configurações do MySQL
db_config = {
    "host": "172.25.2.3",
    "user": "step_root",
    "passwd": "F@087913",
    "database": "step_bd"
}

logging.basicConfig(level=logging.ERROR)

def is_valid_message(message_json):
    return "timestamp" in message_json and "values" in message_json

def on_connect(client, userdata, flags, rc):
    print("Conectado ao broker" if rc == 0 else f"Erro na conexão com o broker, retorno do código: {rc}")
    client.subscribe(topic)

def on_message(client, userdata, msg):
    cursor = None
    db = None
    try:
        try:
            message_json = json.loads(msg.payload.decode("utf-8"))
        except json.JSONDecodeError:
            error_message = f"Erro ao decodificar JSON: '{msg.payload.decode('utf-8')}'"
            print(error_message)
            logging.error(error_message)
            return  # Retorna ao loop principal para processar a próxima mensagem

        if not is_valid_message(message_json):
            warning_message = f"AVISO: Mensagem inválida ignorada: '{msg.payload.decode('utf-8')}'"
            print(warning_message)
            logging.warning(warning_message)
            return  # Retorna ao loop principal para processar a próxima mensagem

        # Conecta ao banco de dados
        db = mysql.connector.connect(**db_config)
        cursor = db.cursor()

        timestamp = message_json['timestamp']
        readings = message_json['values']

        for reading in readings:
            print(f"Reading atual: {reading}")
            plc_id, equipment_id, leitura_entrada = reading.split(',')

            # Consulta para obter id_parametro, id_ponto, id_obra
            join_query = """
            SELECT p.id_parametro, p.id_ponto, o.id_obra
            FROM parametros_ponto AS p
            JOIN pontos_estacao AS pe ON p.id_ponto = pe.id_ponto
            JOIN obras AS o ON pe.id_obra = o.id_obra
            WHERE p.id_sensor_iot = %s
            """
            cursor.execute(join_query, (equipment_id,))
            result = cursor.fetchone()

            if result:
                id_parametro, id_ponto, id_obra = result
                status_leitura = 5
                insert_query = "INSERT INTO rmm (id_obra, id_ponto, id_parametro, leitura_entrada, data_leitura, status_leitura) VALUES (%s, %s, %s, %s, %s, %s)"
                cursor.execute(insert_query, (id_obra, id_ponto, id_parametro, leitura_entrada, timestamp, status_leitura))
                db.commit()
            else:
                print(f"Não foi possível encontrar os dados correspondentes para o equipment_id {equipment_id}")

    except mysql.connector.Error as err:
        print(f"Erro no banco de dados: {err}")
    finally:
        if cursor:
            cursor.close()
        if db:
            db.close()

client = mqtt.Client()
client.on_connect = on_connect
client.on_message = on_message

# Conecta ao servidor MQTT
client.connect(broker_address, broker_port, 60)

# Loop para manter a conexão ativa
client.loop_forever()
