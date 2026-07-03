from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import os
from dotenv import load_dotenv

# Import your functions
from rag_step_3_embeddings import embed_texts
from rag_step_4_vector_db import get_db_collection
from rag_step_6_similarity import retrieve_relevant_chunks
from rag_step_7_prompt import prepare_prompt
from rag_step_8_call_llm import generate_answer

load_dotenv()

app = FastAPI()

class ChatRequest(BaseModel):
    message: str

@app.post("/ask")
async def ask_chatbot(request: ChatRequest):
    try:
        # 1. Get the existing collection (pre-loaded/persisted)
        collection = get_db_collection()
        
        # 2. Embed the user's question
        question_vector = embed_texts([request.message])
        
        # 3. Retrieve relevant chunks
        result = retrieve_relevant_chunks(question_vector, collection, top_k=3)
        
        # 4. Prepare prompt
        prompt = prepare_prompt(request.message, result['documents'][0])
        
        # 5. Get answer
        answer = generate_answer(prompt, os.getenv("DEEPSEEK_API_KEY"))
        
        return {"answer": answer}
        
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))